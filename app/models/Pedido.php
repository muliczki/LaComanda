<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'pedidos';
    public $incrementing = true;
    public $timestamps = false;

    const DELETED_AT = 'fecha_baja';
    const CREATED_AT = 'fecha_alta';

    protected $fillable = [
        'codigo_pedido', 'foto', 'id_mesa', 'id_estado_mesa', 'id_cliente'
    ];

    

}