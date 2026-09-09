<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'role',
        'signer_name',
        'signer_role',
        'signature_data',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
