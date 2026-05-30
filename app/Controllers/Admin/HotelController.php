<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\HotelModel;
use App\Models\HotelPoiDistanceModel;

class HotelController extends BaseController
{
    protected HotelModel $model;
    protected HotelPoiDistanceModel $distanceModel;

    public function __construct()
    {
        $this->model         = new HotelModel();
        $this->distanceModel = new HotelPoiDistanceModel();
    }

    public function index()
    {
        return view('admin/hotels/index', [
            'hotels' => $this->model->getWithAvgDistance(),
        ]);
    }

    public function store()
    {
        if (!$this->validate($this->model->validationRules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $id = $this->model->insert($this->request->getPost());

        // Auto-hitung jarak Haversine ke semua POI
        $this->distanceModel->recalculateForHotel($id);

        return redirect()->to('/admin/hotels')->with('success', 'Hotel berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        if (!$this->validate($this->model->validationRules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, $this->request->getPost());

        // Recalculate jika koordinat berubah
        $this->distanceModel->recalculateForHotel($id);

        return redirect()->to('/admin/hotels')->with('success', 'Hotel berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $this->model->delete($id); // cascade otomatis hapus distances
        return redirect()->to('/admin/hotels')->with('success', 'Hotel berhasil dihapus.');
    }
}