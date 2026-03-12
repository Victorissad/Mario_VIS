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
        if (session('customerId')) {
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

        $passwordMd5 = md5($request->password);

        $response = Http::withToken($this->jwtToken)
            ->post($this->apiUrl . '/customers/verify', [
                'email'    => $request->email,
                'password' => $passwordMd5,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            $customerId = $data['customerId'] ?? -1;

            if ($customerId > 0) {
                session(['customerId' => $customerId]);
                return redirect('/films');
            }
        }

        return back()->withErrors(['login' => 'Email ou mot de passe incorrect.']);
    }

    public function logout()
    {
        session()->forget('customerId');
        return redirect('/login');
    }
}
