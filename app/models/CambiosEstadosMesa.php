<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CambiosEstadosMesa extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'cambiosestadosmesas';
    public $incrementing = true;
    public $timestamps = false;

    const DELETED_AT = 'fecha_baja';
    const CREATED_AT = 'fecha_cambio';

    protected $fillable = [
        'id_mesa', 'id_usuario', 'id_estado_mesa', 'id_codigo_pedido'
    ];

}