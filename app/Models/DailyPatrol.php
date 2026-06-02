<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPatrol extends Model
{
    use HasFactory;

    protected $table = 'daily_patrols';
    protected $primaryKey = 'Id_Daily_Patrol';
    public $timestamps = false;

    protected $fillable = [
        'Name_Daily_Patrol',
        'Time_Daily_Patrol'
    ];

    public function dailyPatrolMembers()
    {
        return $this->hasMany(DailyPatrolMember::class, 'Id_Daily_Patrol', 'Id_Daily_Patrol');
    }
}
