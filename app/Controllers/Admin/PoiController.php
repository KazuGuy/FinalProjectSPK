<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PoiModel;
use App\Models\HotelPoiDistanceModel;

class PoiController extends BaseController
{
    public function store()
    {
        $poiModel      = new PoiModel();
        $distanceModel = new HotelPoiDistanceModel();

        if (!$this->validate($poiModel->validationRules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $id = $poiModel->insert($this->request->getPost());

        // Hitung jarak POI baru ke semua hotel yang sudah ada
        $distanceModel->recalculateForPoi($id);

        return redirect()->to('/admin/poi')->with('success', 'POI berhasil ditambahkan.');
    }
}