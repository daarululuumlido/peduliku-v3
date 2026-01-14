<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Village;

class WilayahController extends Controller
{
    /**
     * Get all provinces.
     */
    public function provinces(): JsonResponse
    {
        $provinces = Cache::remember('indonesia_provinces', 60 * 60 * 24, function () {
            return Province::orderBy('name')->get(['code as id', 'name']);
        });

        return response()->json($provinces);
    }

    /**
     * Get cities by province code.
     */
    public function cities(string $provinceCode): JsonResponse
    {
        $cacheKey = "indonesia_cities_{$provinceCode}";

        $cities = Cache::remember($cacheKey, 60 * 60 * 24, function () use ($provinceCode) {
            return City::where('province_code', $provinceCode)
                ->orderBy('name')
                ->get(['code as id', 'name']);
        });

        return response()->json($cities);
    }

    /**
     * Get districts by city code.
     */
    public function districts(string $cityCode): JsonResponse
    {
        $cacheKey = "indonesia_districts_{$cityCode}";

        $districts = Cache::remember($cacheKey, 60 * 60 * 24, function () use ($cityCode) {
            return District::where('city_code', $cityCode)
                ->orderBy('name')
                ->get(['code as id', 'name']);
        });

        return response()->json($districts);
    }

    /**
     * Get villages by district code.
     */
    public function villages(string $districtCode): JsonResponse
    {
        $cacheKey = "indonesia_villages_{$districtCode}";

        $villages = Cache::remember($cacheKey, 60 * 60 * 24, function () use ($districtCode) {
            return Village::where('district_code', $districtCode)
                ->orderBy('name')
                ->get(['code as id', 'name']);
        });

        return response()->json($villages);
    }

    /**
     * Search villages by name.
     */
    public function searchVillages(Request $request): JsonResponse
    {
        $search = $request->get('q', '');

        if (strlen($search) < 3) {
            return response()->json([]);
        }

        $villages = Village::where('name', 'like', "%{$search}%")
            ->with(['district.city.province'])
            ->limit(20)
            ->get()
            ->map(function ($village) {
                return [
                    'id' => $village->code,
                    'name' => $village->name,
                    'full_name' => "{$village->name}, {$village->district->name}, {$village->district->city->name}, {$village->district->city->province->name}",
                ];
            });

        return response()->json($villages);
    }
}
