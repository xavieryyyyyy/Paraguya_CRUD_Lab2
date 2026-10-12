<?php

// MODEL: This class represents product records. The namespace tells PHP where the class
// belongs; it is not the location of the database itself.
namespace App\Models;

// Import Laravel's Eloquent Model so Product can use database methods.
use Illuminate\Database\Eloquent\Model;

// Extending Model gives us all(), create(), update(), and delete(). Laravel convention connects
// Product to the products table and its id key. Eloquent also manages created_at and updated_at
// unless timestamps are disabled.
class Product extends Model
{
    // These fields may be assigned together through create($data) or update($data). This is
    // called mass assignment. Leave id and timestamps out. IMPORTANT: fillable controls
    // assignment; controller validation checks values. It does not itself reject negative
    // quantities or invalid prices.
    protected $fillable = [
        'name',
        'qty',
        'price',
        'description',
    ];
}