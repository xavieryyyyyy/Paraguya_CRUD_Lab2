<?php

// Class component behind x-app-layout on Dashboard/Profile. render() selects layouts.app, where
// component content is supplied as $slot.


namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
