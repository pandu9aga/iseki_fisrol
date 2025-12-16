<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type_User extends Model
{
    protected $table = 'type_users'; //nama tabel
    protected $primaryKey = 'Id_Type_User'; //pk

    public $timestamps = false;
    
    protected $fillable = [
        'Name_Type_User'
    ];
}
