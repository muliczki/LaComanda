<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstadosMesa extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'estadosmesas';
    public $incrementing = true;
    public $timestamps = false;

    const DELETED_AT = 'fecha_baja';

    protected $fillable = [
        'descripcion'
    ];

}