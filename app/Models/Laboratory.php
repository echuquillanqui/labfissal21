<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    use HasFactory;

    protected $table = 'laboratories';

    protected $fillable = [
        'patient_id',
        'hematocrito', 
        'hemoglobina', 
        'urea_pre', 
        'urea_post', 
        'cloro', 
        'sodio', 
        'potasio', 
        'fosforo', 
        'calcio_total', 
        'tgo', 
        'tgp'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}