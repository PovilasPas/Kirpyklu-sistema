<?php

namespace App\Models;

use App\Models\HairSalon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public $timestamps = false;

    public function HairSalons()
    {
        return $this->hasMany(HairSalon::class, 'city_id');
    }
}
