<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'personas';
    public $incrementing = true;
    public $timestamps = false;

    const DELETED_AT = 'fecha_baja';

    protected $fillable = [
        'nombre', 'apellido', 'email', 'id_sector', 'id_ocupacion', 'id_estado_empleado'
    ];

}

?>