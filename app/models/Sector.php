<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sector extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'sectores';
    public $incrementing = true;
    public $timestamps = false;

    const DELETED_AT = 'fecha_baja';

    protected $fillable = [
        'sector'
    ];

}