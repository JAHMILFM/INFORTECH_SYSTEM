<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftwareBaseline extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'software_ids',
        'is_default',
    ];

    protected $casts = [
        'software_ids' => 'array',
        'is_default'   => 'boolean',
    ];

    public function getSoftwareItemsAttribute()
    {
        if (empty($this->software_ids)) {
            return collect();
        }
        return SoftwareCatalog::whereIn('id', $this->software_ids)->get();
    }
}