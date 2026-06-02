<?php
// app/Controllers/User/HotelController.php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\HotelModel;
use App\Models\PoiModel;

class HotelController extends BaseController
{
    public function search()
    {
        $model = new HotelModel();
        $model->select(
            'hotels.*, (SELECT AVG(jarak_km) FROM hotel_poi_distances hpd WHERE hpd.hotel_id = hotels.id) AS avg_distance',
            false
        );

        $filters = $this->request->getGet();

        // Filter
        if (!empty($filters['q']))
            $model->like('name', $filters['q']);
        if (!empty($filters['min_price']))
            $model->where('price >=', (float) $filters['min_price']);
        if (!empty($filters['max_price']))
            $model->where('price <=', (float) $filters['max_price']);
        if (!empty($filters['min_rating']))
            $model->where('rating >=', (float) $filters['min_rating']);
        if (!empty($filters['min_discount']))
            $model->where('discount >', 0);

        // Sort
        $sortMap = [
            'price_asc'       => ['price', 'ASC'],
            'price_desc'      => ['price', 'DESC'],
            'rating_desc'     => ['rating', 'DESC'],
            'discount_desc'   => ['discount', 'DESC'],
            'facilities_desc' => ['facilities_count', 'DESC'],
        ];
        [$col, $dir] = $sortMap[$filters['sort'] ?? 'rating_desc'] ?? ['rating', 'DESC'];
        $model->orderBy($col, $dir);

        $hotels = $model->paginate(10);
        $pager  = $model->pager;

        return view('user/hotels/search', [
            'hotels'  => $hotels,
            'pager'   => $pager,
            'filters' => $filters,
            'pois'    => (new PoiModel())->findAll(),
        ]);
    }
}
