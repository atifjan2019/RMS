<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Show the home page.
     */
    public function home(): View
    {
        return view('pages.home');
    }
}
