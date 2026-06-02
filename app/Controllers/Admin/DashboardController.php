<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CriteriaModel;
use App\Models\HotelModel;
use App\Models\PoiModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    public function index()
    {
        return view('admin/dashboard', [
            'hotelCount'    => (new HotelModel())->countAllResults(),
            'poiCount'      => (new PoiModel())->countAllResults(),
            'criteriaCount' => (new CriteriaModel())->countAllResults(),
            'userCount'     => (new UserModel())->where('role', 'user')->countAllResults(),
            'hotels'        => array_slice((new HotelModel())->getWithAvgDistance(), 0, 5),
        ]);
    }
}
