<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    // Pastikan 'name' ada di sini
    protected $fillable = [
        'name'
    ];
    
    // Relasi ke Produk (sudah dibahas)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}