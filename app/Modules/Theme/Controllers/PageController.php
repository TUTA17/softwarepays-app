<?php

namespace App\Modules\Theme\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function smm()
    {
        return view('theme::pages.smm');
    }

    public function promotions()
    {
        return view('theme::pages.promotions');
    }

    public function support()
    {
        return view('theme::pages.support');
    }

    public function privacyPolicy()
    {
        return view('theme::pages.privacy');
    }

    public function termsOfService()
    {
        return view('theme::pages.terms');
    }

    public function warrantyPolicy()
    {
        return view('theme::pages.warranty');
    }

    public function other()
    {
        return view('theme::pages.other');
    }
}
