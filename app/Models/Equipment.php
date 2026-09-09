<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equipment';

    protected $fillable = [
        'company_id',
        'type',
        'serial_number',
        'brand',
        'model',
        'hostname',
        'os',
        'notes',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class)->orderBy('service_date', 'desc');
    }

    public function getDisplayNameAttribute()
    {
        return ucfirst($this->type) . ' ' . $this->brand . ' ' . $this->model . ' (' . $this->serial_number . ')';
    }
}
