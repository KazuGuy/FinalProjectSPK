<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\HotelModel;
use App\Models\HotelPoiDistanceModel;
use CodeIgniter\Exceptions\PageNotFoundException;

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

    public function create()
    {
        return view('admin/hotels/form');
    }

    public function edit(int $id)
    {
        $hotel = $this->model->find($id);

        if (!$hotel) {
            throw PageNotFoundException::forPageNotFound('Hotel tidak ditemukan.');
        }

        return view('admin/hotels/form', ['hotel' => $hotel]);
    }

    public function store()
    {
        if (!$this->validate($this->model->validationRules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $data['type_score'] = HotelModel::TYPE_SCORE[$data['type']] ?? 1;

        $id = $this->model->insert($data);
        $this->distanceModel->recalculateForHotel($id);

        return redirect()->to('/admin/hotels')->with('success', 'Hotel berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        if (!$this->validate($this->model->validationRules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $data['type_score'] = HotelModel::TYPE_SCORE[$data['type']] ?? 1;

        $this->model->update($id, $data);
        $this->distanceModel->recalculateForHotel($id);

        return redirect()->to('/admin/hotels')->with('success', 'Hotel berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $this->model->delete($id); // cascade otomatis hapus distances
        return redirect()->to('/admin/hotels')->with('success', 'Hotel berhasil dihapus.');
    }
}
