<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CambiosEstadosEmpleados extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'cambiosestadosempleados';
    public $incrementing = true;
    public $timestamps = false;

    const DELETED_AT = 'fecha_baja';
    const CREATED_AT = 'fecha_registro';

    protected $fillable = [
        'id_persona', 'id_estado_empleado'
    ];

}