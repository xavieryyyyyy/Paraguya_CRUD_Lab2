<?php

// Class component behind x-guest-layout. render() selects layouts.guest, which wraps
// Login/Register/Forgot/Reset pages and loads the custom auth styles.


namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
