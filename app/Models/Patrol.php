<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patrol extends Model
{
    use HasFactory;

    protected $table = 'patrols';
    protected $primaryKey = 'Id_Patrol';
    public $timestamps = false;

    protected $fillable = [
        'Name_Patrol',
        'Time_Patrol'
    ];
    // App\Models\Patrol.php
    public function patrolMembers()
    {
        return $this->hasMany(PatrolMember::class, 'Id_Patrol', 'Id_Patrol');
    }
}
