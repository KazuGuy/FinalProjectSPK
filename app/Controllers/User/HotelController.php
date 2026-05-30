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
        $model   = new HotelModel();
        $builder = $model->builder();

        $filters = $this->request->getGet();

        // Filter
        if (!empty($filters['q']))
            $builder->like('name', $filters['q']);
        if (!empty($filters['min_price']))
            $builder->where('price >=', (float) $filters['min_price']);
        if (!empty($filters['max_price']))
            $builder->where('price <=', (float) $filters['max_price']);
        if (!empty($filters['min_rating']))
            $builder->where('rating >=', (float) $filters['min_rating']);
        if (!empty($filters['min_discount']))
            $builder->where('discount >', 0);

        // Sort
        $sortMap = [
            'price_asc'       => ['price', 'ASC'],
            'price_desc'      => ['price', 'DESC'],
            'rating_desc'     => ['rating', 'DESC'],
            'discount_desc'   => ['discount', 'DESC'],
            'facilities_desc' => ['facilities_count', 'DESC'],
        ];
        [$col, $dir] = $sortMap[$filters['sort'] ?? 'rating_desc'] ?? ['rating', 'DESC'];
        $builder->orderBy($col, $dir);

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