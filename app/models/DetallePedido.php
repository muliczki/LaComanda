<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class DetallePedido extends Model
{

    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'detallepedidos';
    public $incrementing = true;
    public $timestamps = false;

    const DELETED_AT = 'fecha_baja';
    const CREATED_AT = 'fecha_solicitud';
    const UPDATE_AT = 'fecha_actualizacion';

    protected $fillable = [
        'codigo_pedido', 'id_producto', 'id_estado_pedido', 'fecha_estimada', 'id_responsable'
    ];

}