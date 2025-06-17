<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    
    protected $fillable = [
        'nombre_completo',
        'nombre_usuario',
        'password_hash',
        'id_rol'
    ];
    
    protected $hidden = [
        'password_hash',
        'remember_token'
    ];
    
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }
    
    public function rol(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }
}