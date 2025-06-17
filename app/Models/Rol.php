<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id_rol';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id_rol', 
        'nombre_rol'
    ];
}