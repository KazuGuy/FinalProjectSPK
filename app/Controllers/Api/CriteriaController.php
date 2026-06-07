<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\CriteriaModel; // Panggil model yang sudah ada

class CriteriaController extends ResourceController
{
    public function index()
    {
        $model = new CriteriaModel();
        $data = $model->findAll();
        return $this->respond($data); // Langsung kirim JSON ke Flutter
    }

    // Jika diperlukan, bisa tambahkan method lain untuk CRUD (create, update, delete)
    public function create()
    {
        $data = $this->request->getPost();
        if ($this->model->insert($data)) {
            return $this->respondCreated(['status' => 201, 'message' => 'Kriteria berhasil ditambahkan']);
        }
        return $this->fail($this->model->errors());
    }
    public function update($id = null)
    {
        $data = $this->request->getRawInput();
        
        if (!$this->model->find($id)) {
            return $this->failNotFound('Kriteria tidak ditemukan');
        }

        if ($this->model->update($id, $data)) {
            return $this->respond(['status' => 200, 'message' => 'Data berhasil diupdate']);
        }
        return $this->fail('Gagal update data');
    }
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Kriteria tidak ditemukan');
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted(['status' => 200, 'message' => 'Kriteria berhasil dihapus']);
        }
        return $this->fail('Gagal menghapus data');
    }
}