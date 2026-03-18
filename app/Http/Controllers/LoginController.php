<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    private string $apiUrl;
    private string $jwtToken;

    public function __construct()
    {
        $this->apiUrl = config('services.toad.url');
        $this->jwtToken = config('services.toad.token');
    }

    public function showLogin()
    {
        if (session('toad_user')) {
            return redirect('/films');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $response = Http::withToken($this->jwtToken)
            ->post($this->apiUrl . '/staffs/verify', [
                'email'    => $request->email,
                'password' => $request->password,
            ]);

        if ($response->successful()) {
            $resp  = $response->json();
            $staff = $resp['staff'] ?? $resp;

            $userData = [
                'id'    => $staff['staffId'] ?? $staff['id'] ?? $staff['email'],
                'email' => $staff['email'] ?? null,
                'name'  => trim(($staff['firstName'] ?? '') . ' ' . ($staff['lastName'] ?? ''))
                           ?: ($staff['email'] ?? 'Utilisateur'),
                'token' => $resp['token'] ?? $resp['access_token'] ?? null,
                'staff' => $staff,
            ];

            if ($userData['token']) {
                $request->session()->put('toad_user', $userData);
                return redirect('/films');
            }
        }

        return back()->withErrors(['login' => 'Email ou mot de passe incorrect.']);
    }

    public function logout()
    {
        session()->flush();
        return redirect('/login');
    }
}
