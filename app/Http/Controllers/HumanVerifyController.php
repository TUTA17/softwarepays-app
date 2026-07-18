<?php

namespace App\Http\Controllers;

use App\Modules\Core\Services\RecaptchaService;
use Illuminate\Http\Request;

class HumanVerifyController extends Controller
{
    public function form(Request $request)
    {
        $redirect = $request->session()->get('human_verify_redirect', url('/'));
        return view('human-verify', compact('redirect'));
    }

    public function verify(Request $request, RecaptchaService $recaptcha)
    {
        $request->validate(['g-recaptcha-response' => 'required']);

        if (!$recaptcha->verify($request->input('g-recaptcha-response'), $request->ip())) {
            return back()->with('error', __('human_verify.failed_error'));
        }

        $request->session()->put('human_verified', true);
        $redirect = $request->session()->pull('human_verify_redirect', url('/'));

        return redirect($redirect);
    }
}
