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
        $this->model->select(
            'hotels.*, (SELECT AVG(jarak_km) FROM hotel_poi_distances hpd WHERE hpd.hotel_id = hotels.id) AS avg_distance',
            false
        );

        $q = $this->request->getGet('q');
        $minPrice = $this->request->getGet('min_price');
        $maxPrice = $this->request->getGet('max_price');
        $minRating = $this->request->getGet('min_rating');
        $minDiscount = $this->request->getGet('min_discount');
        $type = $this->request->getGet('type');
        $sort = $this->request->getGet('sort') ?? 'rating_desc';

        if (!empty($q)) {
            $this->model->like('name', $q);
        }
        if (!empty($minPrice)) {
            $this->model->where('price >=', (float) $minPrice);
        }
        if (!empty($maxPrice)) {
            $this->model->where('price <=', (float) $maxPrice);
        }
        if (!empty($minRating)) {
            $this->model->where('rating >=', (float) $minRating);
        }
        if (!empty($minDiscount)) {
            $this->model->where('discount >', 0);
        }
        if (!empty($type)) {
            $this->model->where('type', $type);
        }

        $sortMap = [
            'price_asc'       => ['price', 'ASC'],
            'price_desc'      => ['price', 'DESC'],
            'rating_desc'     => ['rating', 'DESC'],
            'discount_desc'   => ['discount', 'DESC'],
            'facilities_desc' => ['facilities_count', 'DESC'],
        ];
        [$col, $dir] = $sortMap[$sort] ?? ['rating', 'DESC'];
        $this->model->orderBy($col, $dir);

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
        $data['type_score'] = \App\Models\HotelModel::TYPE_SCORE[$data['type'] ?? ''] ?? 1;

        if ($this->model->insert($data)) {
            $id = $this->model->getInsertID();
            $distanceModel = new \App\Models\HotelPoiDistanceModel();
            $distanceModel->recalculateForHotel($id);
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

        if (isset($data['type'])) {
            $data['type_score'] = \App\Models\HotelModel::TYPE_SCORE[$data['type']] ?? 1;
        }

        if ($this->model->update($id, $data)) {
            $distanceModel = new \App\Models\HotelPoiDistanceModel();
            $distanceModel->recalculateForHotel($id);
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