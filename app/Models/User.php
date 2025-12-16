<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users'; //nama tabel
    protected $primaryKey = 'Id_User'; //pk

    public $timestamps = false;

    protected $fillable = [
        'Username_User',
        'Name_User',
        'Password_User',
        'Id_Type_User'
    ];

    public function type_user()
    {
        return $this->belongsTo(Type_User::class, 'Id_Type_User', 'Id_Type_User');
    }

    // app/Models/User.php

    public function getDisplayNameAttribute()
    {
        return $this->Name_User;
    }
}
