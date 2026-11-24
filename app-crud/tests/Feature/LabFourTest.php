<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LabFourTest extends TestCase
{
    use RefreshDatabase;

    private function productData(): array
    {
        return ['name' => 'Apple', 'qty' => 5, 'price' => '12.50'];
    }

    private function signInAsAdmin(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    private function image(): UploadedFile
    {
        // Use real PNG bytes so Laravel can validate the content without PHP GD.
        return UploadedFile::fake()->createWithContent('product.png', base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADElEQVR4nGMQjWwCAAF3APHHv0M/AAAAAElFTkSuQmCC'
        ));
    }

    public function test_registration_cannot_assign_admin_privileges(): void
    {
        $this->post('/register', [
            'name' => 'Regular User',
            'email' => 'regular@example.test',
            'password' => 'test-password',
            'password_confirmation' => 'test-password',
            'role' => 'admin',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'regular@example.test', 'role' => 'user']);
    }

    public function test_regular_user_can_view_but_cannot_manage_products(): void
    {
        $product = Product::create($this->productData());
        $url = route('product.show', $product, absolute: false);

        $user = User::factory()->create();
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('product.index'));
        $this->get('/product')->assertOk()->assertSee('Apple')->assertDontSee('Add Product');
        $this->get($url)->assertOk()->assertSee('Apple')->assertDontSee('Edit Product');
        $this->get('/product/create')->assertForbidden();
        $this->post('/product', $this->productData())->assertForbidden();
        $this->get($url.'/edit')->assertForbidden();
        $this->put($url, ['qty' => 99] + $this->productData())->assertForbidden();
        $this->delete($url)->assertForbidden();
        $this->assertDatabaseCount('products', 1);
        $this->assertSame(5, $product->refresh()->qty);
    }

    public function test_invalid_product_data_is_rejected_and_previous_input_is_kept(): void
    {
        $this->signInAsAdmin();
        $this->post('/product', [])->assertSessionHasErrors(['name', 'qty', 'price']);
        $this->from('/product/create')->post('/product', [
            'name' => str_repeat('a', 256), 'qty' => '2.5', 'price' => '-500',
        ])->assertRedirect('/product/create')
            ->assertSessionHasErrors(['name', 'qty', 'price'])
            ->assertSessionHasInput('price', '-500');
        $this->assertDatabaseCount('products', 0);
    }

    public function test_admin_can_upload_replace_and_delete_an_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('product.index'));
        $this->get('/product/create')->assertOk();
        $this->post('/product', $this->productData() + ['image' => $this->image()])
            ->assertRedirect(route('product.index'));
        $product = Product::sole();
        $original = $product->image_path;
        $disk = Storage::disk('public');
        $disk->assertExists($original);
        $url = route('product.show', $product, absolute: false);
        // The dev server may listen on a different host or port than APP_URL.
        $origin = 'http://127.0.0.1:8001';
        $expectedImage = 'src="'.$origin.'/storage/'.$original.'"';
        $this->get($origin.'/product')->assertOk()->assertSee($expectedImage, false);
        $this->get($origin.$url)->assertOk()->assertSee($expectedImage, false);
        $this->get($origin.$url.'/edit')->assertOk()->assertSee($expectedImage, false);

        $this->put($url, $this->productData())->assertSessionHasNoErrors();
        $this->assertSame($original, $product->refresh()->image_path);
        $this->put($url, ['price' => '-1'] + $this->productData() + ['image' => $this->image()])
            ->assertSessionHasErrors('price');
        $textFile = UploadedFile::fake()->createWithContent('pretend.png', 'plain text');
        // Use the real file's content for MIME detection, even though the client says PNG.
        $disguisedFile = new UploadedFile($textFile->getPathname(), 'pretend.png', 'image/png', null, true);
        $this->put($url, $this->productData() + ['image' => $disguisedFile])
            ->assertSessionHasErrors('image');
        $this->assertSame($original, $product->refresh()->image_path);
        $this->assertSame([$original], $disk->allFiles('products'));

        $this->put($url, $this->productData() + ['image' => $this->image()])
            ->assertRedirect(route('product.index'));
        $replacement = $product->refresh()->image_path;
        $this->assertNotSame($original, $replacement);
        $disk->assertMissing($original);
        $disk->assertExists($replacement);

        $this->delete($url)->assertRedirect(route('product.index'));
        $this->assertModelMissing($product);
        $disk->assertMissing($replacement);
    }

    public function test_oversized_image_is_rejected(): void
    {
        Storage::fake('public');
        $this->signInAsAdmin();
        $this->post('/product', $this->productData() + ['image' => $this->image()->size(2049)])
            ->assertSessionHasErrors('image');
        $this->assertDatabaseCount('products', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_storage_failure_keeps_the_previous_image(): void
    {
        $disk = Storage::fake('public');
        $oldPath = 'products/existing.png';
        $disk->put($oldPath, $this->image()->get());
        $product = Product::create($this->productData() + ['image_path' => $oldPath]);
        $this->signInAsAdmin();

        $failedDisk = \Mockery::mock(FilesystemAdapter::class);
        $failedDisk->shouldReceive('putFileAs')->once()->andReturn(false);
        $this->mock(FilesystemFactory::class)
            ->shouldReceive('disk')->with('public')->once()->andReturn($failedDisk);

        $this->put(route('product.update', $product), ['name' => 'Changed'] + $this->productData() + [
            'image' => $this->image(),
        ])->assertSessionHasErrors('image');

        $this->assertSame($oldPath, $product->refresh()->image_path);
        $this->assertSame('Apple', $product->name);
        $disk->assertExists($oldPath);
    }

    public function test_admin_can_be_assigned_to_an_existing_account_from_artisan(): void
    {
        $user = User::factory()->create();

        $this->artisan('users:make-admin', ['email' => $user->email])
            ->expectsConfirmation('Grant admin access to '.$user->email.'?', 'yes')
            ->assertSuccessful();

        $this->assertSame('admin', $user->refresh()->role);
    }

    public function test_name_search_keeps_keyword_across_pages_and_shows_no_matches(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            Product::create(['name' => sprintf('Apple %02d', $i)] + $this->productData());
        }
        Product::create(['name' => 'Banana'] + $this->productData());

        $this->actingAs(User::factory()->create())
            ->get('/product?search=Apple')
            ->assertOk()->assertSee('Apple 01')->assertDontSee('Apple 11')->assertDontSee('Banana')
            ->assertSee('search=Apple&amp;page=2', false);
        $this->get('/product?search=Apple&page=2')
            ->assertOk()->assertSee('Apple 11')->assertDontSee('Apple 01');
        $this->get('/product?search=Orange')->assertOk()->assertSee('No products match');
    }
}
