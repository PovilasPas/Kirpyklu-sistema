<?php

namespace App\Models;

use App\Models\City;
use App\Models\User;
use App\Models\Hairdresser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HairSalon extends Model
{
    use HasFactory;

    protected $table = 'hair_salons';

    protected $fillable = [
        'name',
        'address',
        'description',
        'manager_id',
        'city_id',
    ];

    public function hairdressers()
    {
        return $this->hasMany(Hairdresser::class, 'hair_salon_id');
    }

    public function approved_hairdressers()
    {
        return $this->hasMany(Hairdresser::class, 'hair_salon_id')->where('is_approved', 1);
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
