<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatrolMember extends Model
{
    protected $table = 'patrol_members';
    protected $primaryKey = 'Id_Patrol_Member';
    public $timestamps = false;

    protected $fillable = [
        'Id_Patrol',
        'Id_User',
        'Id_Member',
    ];

    public function patrol()
    {
        return $this->belongsTo(Patrol::class, 'Id_Patrol', 'Id_Patrol');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'Id_User', 'Id_User');
    }

    public function typeUser()
    {
        return $this->belongsTo(Type_User::class, 'Id_Type_User', 'Id_Type_User');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'Id_Member', 'Id_Member');
    }
}
