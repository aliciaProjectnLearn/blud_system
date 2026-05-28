<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsGateway extends Model
{
    use HasFactory;

    protected $table = 'cms_gateways';

    protected $fillable = ['key', 'label', 'grup', 'value', 'tipe', 'urutan'];

    public static function getValue(string $key, string $default = ''): string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function getByGrup(): array
    {
        return static::orderBy('urutan')
            ->get()
            ->groupBy('grup')
            ->toArray();
    }
}
