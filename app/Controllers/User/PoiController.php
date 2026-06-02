<?php
namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\PoiModel;

class PoiController extends BaseController
{
    public function index()
    {
        return view('user/poi/index', [
            'pois' => (new PoiModel())->orderBy('nama_poi')->findAll(),
        ]);
    }
}
