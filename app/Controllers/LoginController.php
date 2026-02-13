<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class LoginController extends Controller
{
    public function __construct()
    {
        if (auth()->loggedIn()) {
            return redirect()->route('user.home.index'); // Redirect to dashboard after successful login
        }
    }

    public function index()
    {
        if (auth()->loggedIn()) {
            return redirect()->route('user.home.index'); // Redirect to dashboard after successful login
        }
        return view('login');
    }

    public function authenticate()
    {
        if (auth()->loggedIn()) {
            return redirect()->route('user.home.index'); // Redirect to dashboard after successful login
        }

        $credentials = [
            'username'    => $this->request->getPost('username'),
            'password' => $this->request->getPost('password')
        ];
        
        $loginAttempt = auth()->attempt($credentials);
        
        if (! $loginAttempt->isOK()) {
            return redirect()->back()->with('error', $loginAttempt->reason());
        }

        return redirect()->route('user.home.index'); // Redirect to dashboard after successful login
    }

    public function logout()
    {
        auth()->logout();

        return redirect()->route('user.login.index');
    }

    public function checkRememberMe()
    {
        if(auth()->loggedIn()) {
            return redirect()->route('user.home.index');
        }

        return redirect()->route('user.login.index');
    }
}