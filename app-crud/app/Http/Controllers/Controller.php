<?php

// Shared parent of the application controllers. Concrete controllers contain the request
// methods; this empty abstract class keeps a common place for future shared behavior.


namespace App\Http\Controllers;

// Laravel scaffold: the shared parent of the ProductController. abstract means it is a base for
// subclasses, not an object we instantiate directly. This lab leaves it empty; the CRUD methods
// live in ProductController.php.
abstract class Controller
{
    // No additional code is needed in this generated section for the CRUD lab.
}
