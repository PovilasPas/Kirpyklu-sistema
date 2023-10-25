<?php

namespace App\Models;

use App\Models\User;
use App\Models\HairSalon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hairdresser extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'phone_nr',
        'is_approved',
        'hair_salon_id'
    ];

    protected $attributes = [
        'is_approved' => 0
    ];

    public function hairSalon()
    {
        return $this->belongsTo(HairSalon::class, 'hair_salon_id');
    }

    public function hairstyles()
    {
        return $this->hasMany(Hairstyle::class, 'hairdresser_id');
    }
}
