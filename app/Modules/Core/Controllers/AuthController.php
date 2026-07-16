<?php

namespace App\Modules\Core\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLogin()
    {
        if (auth()->guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('core::auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|min:3',
        ]);

        $result = $this->authService->login(
            $request->input('email'),
            $request->input('password'),
            $request->has('remember_account')
        );

        if ($result['success']) {
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => $result['message']])->withInput();
    }

    public function logout()
    {
        $this->authService->logout();
        return redirect()->route('admin.login');
    }
}
