<?php
namespace App\Controllers\Api;
use CodeIgniter\RESTful\ResourceController;
class PoiController extends ResourceController
{
    protected $modelName = 'App\Models\PoiModel';
    protected $format    = 'json';

    // READ: Mengambil semua data
    public function index()
    {
        return $this->respond([
            'status' => 200,
            'message' => 'Data POI berhasil dimuat',
            'data' => $this->model->findAll()
        ]);
    }

    // CREATE: Menambah data baru
    public function create()
    {
        $data = $this->request->getPost();
        if ($this->model->insert($data)) {
            return $this->respondCreated(['status' => 201, 'message' => 'POI berhasil ditambahkan']);
        }
        return $this->fail($this->model->errors());
    }

    // UPDATE: Mengubah data
    public function update($id = null)
    {
        // Mengambil data dari request JSON/PUT
        $data = $this->request->getRawInput();
        
        if (!$this->model->find($id)) {
            return $this->failNotFound('POI tidak ditemukan');
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
            return $this->failNotFound('POI tidak ditemukan');
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted(['status' => 200, 'message' => 'POI berhasil dihapus']);
        }
        return $this->fail('Gagal menghapus data');
    }
}
