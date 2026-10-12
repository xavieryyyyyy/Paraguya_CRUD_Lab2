<?php

// MIGRATION: A versioned description of table structure, not a saved product. Migration is the
// base class; Blueprint describes columns; Schema manages tables.
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Laravel receives an anonymous Migration object from this file.
return new class extends Migration
{
    /**
     * Run the migrations.


 * up() creates the table when this migration is pending and artisan migrate runs.


 * : void means the method does not return a value. Laravel records completed


 * migrations, so running migrate again does not repeat them. Editing this file


 * after it has run does not automatically change the existing database table.
 */
    public function up(): void
{
    // Create products using the database connection configured in .env. This name follows
    // Laravel's Product -> products table convention.
    Schema::create('products', function (Blueprint $table) {
        // Auto-incrementing primary key: a unique identifier for each row.
        $table->id();
        // Short text for the product name; string defaults to a 255-character limit.
        $table->string('name');
        // A whole-number quantity. Our controller separately rejects negative input.
        $table->integer('qty');
        // Exact decimal storage: eight total digits, with two after the decimal point. Example:
        // 25.00. Decimal is preferable to approximate floating-point storage for this price
        // field; the controller also enforces the permitted range.
        $table->decimal('price', 8, 2);
        // Longer text; nullable permits a SQL NULL value when there is no description.
        $table->text('description')->nullable();
        // Add created_at and updated_at columns managed by Eloquent.
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.


 * down() reverses this migration during rollback. Dropping the products table


 * removes its data too; this is not how we delete one product in normal use.
 */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
