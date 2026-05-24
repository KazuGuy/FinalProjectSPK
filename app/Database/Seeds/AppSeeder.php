<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AppSeeder extends Seeder
{
    public function run()
    {
        // Data akun Admin utama yang didaftarkan oleh sistem
        $dataAdmin = [
            'name'       => 'Admin',
            'email'      => 'admin@mabac.pro',
            'password'   => password_hash('admin123', PASSWORD_BCRYPT), // Password terenkripsi
            'role'       => 'admin',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Memasukkan data ke tabel 'users' menggunakan Query Builder
        // ignore() mencegah error jika perintah ini tidak sengaja dijalankan dua kali
        $this->db->table('users')->ignore()->insert($dataAdmin);
        
        echo "Seeder Sukses: Akun Admin utama telah berhasil didaftarkan!\n";
    }
}