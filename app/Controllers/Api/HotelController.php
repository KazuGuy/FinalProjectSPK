<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\HotelModel;

class HotelController extends ResourceController
{
    protected $modelName = 'App\Models\HotelModel';
    protected $format    = 'json';

    // READ: Mengambil semua data
    public function index()
    {
        return $this->respond([
            'status' => 200,
            'message' => 'Data hotel berhasil dimuat',
            'data' => $this->model->findAll()
        ]);
    }

    // CREATE: Menambah data baru
    public function create()
    {
        $data = $this->request->getPost();
        if ($this->model->insert($data)) {
            return $this->respondCreated(['status' => 201, 'message' => 'Hotel berhasil ditambahkan']);
        }
        return $this->fail($this->model->errors());
    }

    // UPDATE: Mengubah data
    public function update($id = null)
    {
        // Mengambil data dari request JSON/PUT
        $data = $this->request->getRawInput();
        
        if (!$this->model->find($id)) {
            return $this->failNotFound('Hotel tidak ditemukan');
        }

        if ($this->model->update($id, $data)) {
            return $this->respond(['status' => 200, 'message' => 'Data berhasil diupdate']);
        }
        return $this->fail('Gagal update data');
    }

    // DELETE: Menghapus data
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Hotel tidak ditemukan');
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted(['status' => 200, 'message' => 'Hotel berhasil dihapus']);
        }
        return $this->fail('Gagal menghapus data');
    }
}