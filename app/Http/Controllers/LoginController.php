<?php

namespace App\Http\Controllers;

use App\Auth\ToadUser;
use App\Services\ToadAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $redirectTo = '/films';
    protected $toadAuth;

    public function __construct(ToadAuthService $toadAuth)
    {
        $this->toadAuth = $toadAuth;
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect($this->redirectTo);
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $resp = $this->toadAuth->verify(
            $request->input('email'),
            $request->input('password')
        );

        if (!$resp) {
            throw ValidationException::withMessages([
                'login' => ['Email ou mot de passe incorrect.'],
            ]);
        }

        $staff = $resp['staff'] ?? $resp;

        $userData = [
            'id'    => $staff['staffId'] ?? $staff['id'] ?? $staff['email'],
            'email' => $staff['email'] ?? null,
            'name'  => trim(($staff['firstName'] ?? '') . ' ' . ($staff['lastName'] ?? ''))
                       ?: ($staff['email'] ?? 'Utilisateur'),
            'token' => $resp['token'] ?? $resp['access_token'] ?? null,
            'staff' => $staff,
        ];

        $request->session()->put('toad_user', $userData);

        $user = new ToadUser($userData);
        Auth::login($user, false);

        return redirect($this->redirectTo);
    }

    public function logout()
    {
        Auth::logout();
        session()->flush();
        return redirect('/login');
    }
}
