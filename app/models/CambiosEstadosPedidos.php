<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CambiosEstadosPedidos extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'cambiosestadospedidos';
    public $incrementing = true;
    public $timestamps = false;

    const DELETED_AT = 'fecha_baja';
    const CREATED_AT = 'fecha_registro';

    protected $fillable = [
        'id_codigo_pedido', 'id_usuario', 'id_detalle_pedido', 'id_estado_pedido'
    ];

}