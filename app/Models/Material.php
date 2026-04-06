<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['name', 'karat', 'current_rate', 'unit'];

    public function products()
    {
        return $this->hasMany(Product::class, 'material_id');
    }
}

