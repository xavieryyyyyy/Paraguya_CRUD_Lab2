<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

// Verify the lab's custom redirects and access boundary using in-memory test data.
// phpunit.xml selects SQLite :memory:, so these records are not the local MySQL records.
class LabThreeFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_sends_authenticated_users_to_products(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/')->assertRedirect(route('product.index'));
    }

    public function test_all_six_product_endpoints_require_login(): void
    {
        $product = Product::create([
            'name' => 'Test Apple', 'qty' => 2, 'price' => '10.00', 'description' => null,
        ]);

        // Check display and write endpoints. A guest must not reach any controller action.
        $this->get('/product')->assertRedirect(route('login'));
        $this->get('/product/create')->assertRedirect(route('login'));
        $this->get('/product/'.$product->id.'/edit')->assertRedirect(route('login'));
        $this->post('/product', ['name' => 'Blocked'])->assertRedirect(route('login'));
        $this->put('/product/'.$product->id, ['qty' => 99])->assertRedirect(route('login'));
        $this->delete('/product/'.$product->id)->assertRedirect(route('login'));

        // The rejected requests must leave the one existing test product unchanged.
        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'qty' => 2]);
    }

    public function test_login_returns_to_the_original_protected_page(): void
    {
        $user = User::factory()->create();
        $this->get('/product/create')->assertRedirect(route('login'));
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/product/create');
        $this->assertAuthenticated();
    }

    public function test_duplicate_email_and_mismatched_password_are_rejected(): void
    {
        $user = User::factory()->create();
        $this->post('/register', [
            'name' => 'Duplicate User', 'email' => $user->email,
            'password' => 'test-password', 'password_confirmation' => 'different-password',
        ])->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_remembered_login_issues_a_recaller_cookie(): void
    {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email, 'password' => 'password', 'remember' => '1',
        ]);
        $response->assertRedirect(route('product.index', absolute: false));
        $response->assertCookie(Auth::guard('web')->getRecallerName());
        $this->assertAuthenticated();
    }

    public function test_authenticated_product_view_contains_logout_form(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/product')
            ->assertOk()
            ->assertSee('action="'.route('logout').'"', false)
            ->assertSee('Log out');
    }
}
