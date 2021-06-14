<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoginUsers extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'loginusers';
    public $incrementing = true;
    public $timestamps = false;

    const DELETED_AT = 'fecha_baja';
    const CREATED_AT = 'fecha_conexion';

    protected $fillable = [
        'id_usuario', 'id_ocupacion'
    ];

}