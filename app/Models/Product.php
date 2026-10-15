<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected function imageUrl(): Attribute
    {
        return Attribute::make(get: fn (): string => preg_match('#^https?://#i', $this->image) ? $this->image : asset($this->image));
    }

    protected $fillable = [
        'name',
        'category',
        'description',
        'price',
        'stock',
        'image',
    ];
}
