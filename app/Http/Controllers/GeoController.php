<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\CityMunicipality;
use App\Models\Barangay;

class GeoController extends Controller
{
    public function provinces(string $region)
    {
        return Province::where('region_psgc', $region)
            ->orderBy('name')
            ->get(['psgc_code', 'name']);
    }

    public function cities(string $province)
    {
        return CityMunicipality::where('province_psgc', $province)
            ->orderBy('name')
            ->get(['psgc_code', 'name', 'geo_level', 'zip_code']);
    }

    public function barangays(string $city)
    {
        return Barangay::where('citymun_psgc', $city)
            ->orderBy('name')
            ->get(['psgc_code', 'name']);
    }
}
