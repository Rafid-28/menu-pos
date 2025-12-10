<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'price',
        'is_available', // Pastikan ini ada dan namanya benar
    ]; 
    
    // WAJIB: Casting is_available ke boolean agar database menerima 0/1
    protected $casts = [
        'is_available' => 'boolean',
    ];
    
    // Relasi ke Kategori (sudah dibahas)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}