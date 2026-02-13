<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function authenticate()
    {
        // Get username, password, and remember me from POST
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $rememberMe = $this->request->getPost('remember_me'); // Check if "remember me" was selected

        // Load the User model
        $userModel = new UserModel();

        // Find the user by username
        $user = $userModel->where('username', $username)->first();
        
        if ($user && password_verify($password, $user['password'])) {
            // Store user data in session
            $session = session();
            $session->set([
                'isLoggedIn' => true,
                'user_id' => $user['id'],
                'username' => $user['username']
            ]);

            // If remember me is checked, create a remember me token
            if ($rememberMe) {
                $rememberToken = bin2hex(random_bytes(32)); // Generate a random token
                $userModel->update($user['id'], [
                    'remember_token' => $rememberToken
                ]);

                // Set the token in the user's cookies (expires in 30 days)
                set_cookie('remember_token', $rememberToken, 60 * 60 * 24 * 30, '/', '', false, true);
                set_cookie('remember_user_id', $user['id'], 60 * 60 * 24 * 30, '/', '', false, true);
            }

            return redirect()->to('/'); // Redirect to dashboard after successful login
        } else {
            // Show error if authentication fails
            return redirect()->to('/login')->with('error', 'Invalid username or password');
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();

        // Delete remember me cookies on logout
        delete_cookie('remember_token');
        delete_cookie('remember_user_id');

        return redirect()->to('/login');
    }

    public function checkRememberMe()
    {
        $userModel = new UserModel();

        // Check if cookies are set
        $rememberToken = get_cookie('remember_token');
        $rememberUserId = get_cookie('remember_user_id');

        if ($rememberToken && $rememberUserId) {
            // Check if the remember token exists in the database
            $user = $userModel->find($rememberUserId);

            if ($user && $user['remember_token'] === $rememberToken) {
                // Log the user in automatically
                $session = session();
                $session->set([
                    'isLoggedIn' => true,
                    'user_id' => $user['id'],
                    'username' => $user['username']
                ]);

                return redirect()->to('/dashboard');
            }
        }

        // If the remember me cookies are not valid, redirect to login
        return redirect()->to('/login');
    }
}