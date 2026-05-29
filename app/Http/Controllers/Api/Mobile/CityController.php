<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Support\InertiaProps;
use Illuminate\Http\JsonResponse;

class CityController extends Controller
{
    public function index(): JsonResponse
    {
        $cities = City::query()
            ->orderBy('name')
            ->get()
            ->map(fn (City $city) => InertiaProps::city($city))
            ->values();

        return response()->json([
            'data' => $cities,
        ]);
    }
}
