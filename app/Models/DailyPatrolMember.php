<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyPatrolMember extends Model
{
    protected $table = 'daily_patrol_members';
    protected $primaryKey = 'Id_Daily_Patrol_Member';
    public $timestamps = false;

    protected $fillable = [
        'Id_Daily_Patrol',
        'Id_User',
        'Id_Member',
    ];

    public function dailyPatrol()
    {
        return $this->belongsTo(DailyPatrol::class, 'Id_Daily_Patrol', 'Id_Daily_Patrol');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'Id_User', 'Id_User');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'Id_Member', 'id');
    }
}
