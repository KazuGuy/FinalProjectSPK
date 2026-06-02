<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\HotelPoiDistanceModel;
use App\Models\PoiModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PoiController extends BaseController
{
    protected PoiModel $model;
    protected HotelPoiDistanceModel $distanceModel;

    public function __construct()
    {
        $this->model         = new PoiModel();
        $this->distanceModel = new HotelPoiDistanceModel();
    }

    public function index()
    {
        return view('admin/poi/index', [
            'pois' => $this->model->orderBy('nama_poi')->findAll(),
        ]);
    }

    public function create()
    {
        return view('admin/poi/form');
    }

    public function store()
    {
        if (!$this->validate($this->model->validationRules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        $id = $this->model->insert($this->request->getPost());
        $this->distanceModel->recalculateForPoi($id);
        return redirect()->to('/admin/poi')->with('success', 'POI berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $poi = $this->model->find($id);
        if (!$poi) {
            throw PageNotFoundException::forPageNotFound('POI tidak ditemukan.');
        }
        return view('admin/poi/form', ['poi' => $poi]);
    }

    public function update(int $id)
    {
        if (!$this->validate($this->model->validationRules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        $this->model->update($id, $this->request->getPost());
        $this->distanceModel->recalculateForPoi($id);
        return redirect()->to('/admin/poi')->with('success', 'POI berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $this->model->delete($id);
        return redirect()->to('/admin/poi')->with('success', 'POI berhasil dihapus.');
    }
}