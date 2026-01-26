<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityMunicipality extends Model
{
    protected $table = 'city_municipalities';
    protected $primaryKey = 'psgc_code';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'psgc_code', 'region_psgc', 'province_psgc', 'name', 'geo_level', 'zip_code'
    ];
}
