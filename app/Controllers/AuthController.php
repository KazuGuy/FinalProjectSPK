<?php
// app/Controllers/AuthController.php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        // Kalau sudah login, redirect sesuai role
        if (session()->get('user')) {
            return $this->redirectByRole();
        }

        return view('auth/login');
    }

    public function loginProcess()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = (new UserModel())->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email atau password salah.');
        }

        session()->set('user', [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ]);

        return $this->redirectByRole();
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    private function redirectByRole()
    {
        $role = session()->get('user')['role'];
        return $role === 'admin'
            ? redirect()->to('/admin/criteria')
            : redirect()->to('/hotels');
    }
}