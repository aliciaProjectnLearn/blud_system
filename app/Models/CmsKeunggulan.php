<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsKeunggulan extends Model
{
    use HasFactory;

    protected $fillable = ['judul', 'deskripsi', 'icon_svg', 'urutan', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query) { return $query->where('is_active', true); }
    public function scopeOrdered($query) { return $query->orderBy('urutan'); }
}
