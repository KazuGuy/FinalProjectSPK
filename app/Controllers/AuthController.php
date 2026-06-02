<?php
// app/Controllers/AuthController.php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        $user = session()->get('user');

        if ($user && ($user['role'] ?? null) !== 'guest') {
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

    public function register()
    {
        $user = session()->get('user');

        if ($user && ($user['role'] ?? null) !== 'guest') {
            return $this->redirectByRole();
        }

        return view('auth/register');
    }

    public function registerProcess()
    {
        $rules = [
            'name'             => 'required|max_length[100]',
            'email'            => 'required|valid_email|max_length[100]|is_unique[users.email]',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model = new UserModel();
        $id = $model->insert([
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role'     => 'user',
        ]);

        if (!$id) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $model->errors());
        }

        $user = $model->find($id);
        session()->set('user', [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ]);

        return redirect()->to('/hotels')->with('success', 'Akun berhasil dibuat. Anda sudah masuk sebagai pengguna.');
    }

    public function guestLogin()
    {
        session()->set('user', [
            'id'    => null,
            'name'  => 'Guest',
            'email' => null,
            'role'  => 'guest',
        ]);

        return redirect()->to('/hotels')->with('success', 'Anda masuk sebagai guest. Evaluasi DSS tersedia setelah daftar atau login.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    private function redirectByRole()
    {
        $role = session()->get('user')['role'];

        if ($role === 'admin') {
            return redirect()->to('/admin/dashboard');
        }

        return redirect()->to('/hotels');
    }
}
