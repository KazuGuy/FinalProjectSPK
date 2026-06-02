<?php
// app/Filters/RoleFilter.php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session()->get('user');

        if (!$user) {
            return redirect()->to('/login')
                ->with('error', 'Silakan login atau daftar untuk mengakses halaman tersebut.');
        }

        if ($arguments && !in_array($user['role'], $arguments)) {
            if ($user['role'] === 'guest') {
                return redirect()->to('/hotels')
                    ->with('error', 'Guest hanya dapat mencari hotel dan informasi POI. Daftar atau login untuk memakai evaluasi DSS.');
            }

            return redirect()->to('/hotels')->with('error', 'Akses ditolak.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null){}
}
