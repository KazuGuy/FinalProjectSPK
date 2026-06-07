<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class AuthController extends BaseController
{
    use ResponseTrait;

    // ==========================================
    // FUNGSI LOGIN (AKUN LAMA & BARU BISA MASUK)
    // ==========================================
    public function login()
    {
        // Jalur pintas CORS agar HP Fisik bisa akses backend
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return $this->response->setStatusCode(200);
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $db   = \Config\Database::connect();
        $user = $db->table('users')->where('email', $email)->get()->getRowArray();

        if ($user) {
            $isPasswordValid = false;

            // 1. Jika password di DB berupa hash (BCRYPT ditandai dengan awalan $2y$)
            if (str_starts_with($user['password'], '$2y$')) {
                if (password_verify($password, $user['password'])) {
                    $isPasswordValid = true;
                }
            } else {
                // 2. Jika password di DB berupa akun lama (Plain Text/Teks Biasa)
                if ($password === $user['password']) {
                    $isPasswordValid = true;
                }
            }

            // Jika password cocok (baik cara 1 atau cara 2)
            if ($isPasswordValid) {
                return $this->respond([
                    'status'  => 200,
                    'message' => 'Login Berhasil',
                    'data'    => [
                        'id'    => $user['id'],
                        'name'  => $user['name'] ?? $user['username'] ?? 'User',
                        'email' => $user['email'],
                        'role'  => strtolower($user['role']) // Memaksa teks role kecil semua ('admin' / 'user')
                    ]
                ], 200);
            }
        }

        return $this->respond([
            'status'  => 401,
            'message' => 'Email atau Password Salah'
        ], 401);
    }

    // ==========================================
    // FUNGSI REGISTER (OTOMATIS HASH PASSWORD)
    // ==========================================
    public function register()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return $this->response->setStatusCode(200);
        }

        $name     = $this->request->getPost('name');
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $role     = $this->request->getPost('role') ?? 'user';

        $db = \Config\Database::connect();
        
        // Validasi duplikasi email
        $existingUser = $db->table('users')->where('email', $email)->get()->getRowArray();
        if ($existingUser) {
            return $this->respond([
                'status'  => 400,
                'message' => 'Email sudah terdaftar!'
            ], 400);
        }

        $data = [
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT), // Hasil register di-hash aman
            'role'     => $role
        ];

        $db->table('users')->insert($data);

        return $this->respond([
            'status'  => 201,
            'message' => 'Registrasi berhasil!'
        ], 201);
    }
}