<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hairstyle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'image',
        'estimated_time_min',
        'hairdresser_id'
    ];

    public function hairdresser()
    {
        return $this->belongsTo(hairdresser::class, 'hairdresser_id');
    }
}
