<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSoftware extends Model
{
    use HasFactory;

    protected $table = 'report_software';

    protected $fillable = [
        'report_id',
        'software_id',
        'software_name',
        'detail',
        'is_installed',
    ];

    protected $casts = [
        'is_installed' => 'boolean',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function catalogItem()
    {
        return $this->belongsTo(SoftwareCatalog::class, 'software_id');
    }

    public function getEffectiveNameAttribute(): string
    {
        if ($this->catalogItem) {
            return $this->catalogItem->name;
        }
        return $this->software_name ?? 'Otro Software';
    }
}
