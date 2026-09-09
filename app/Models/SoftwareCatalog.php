<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftwareCatalog extends Model
{
    use HasFactory;

    protected $table = 'software_catalog';

    protected $fillable = [
        'name',
        'category',
        'requires_detail',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'requires_detail' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function reportSoftware()
    {
        return $this->hasMany(ReportSoftware::class, 'software_id');
    }
}
