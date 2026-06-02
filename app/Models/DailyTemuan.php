<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DailyTemuan extends Model
{
    protected $table = 'daily_temuans';
    protected $primaryKey = 'Id_Daily_Temuan';
    public $timestamps = false;

    protected $fillable = [
        'Path_Daily_Temuan',
        'Desc_Daily_Temuan',
        'Path_Daily_Update_Temuan',
        'Desc_Daily_Update_Temuan',
        'Id_Daily_Patrol',
        'Id_User',
        'Status_Daily_Temuan',
        'pic_proses_nik_daily',
        'Rotate_Daily_Temuan',
        'Rotate_Daily_Update'
    ];

    public function getPicProsesNameAttribute()
    {
        if (!$this->pic_proses_nik_daily) return null;
        $emp = DB::connection('rifa')
            ->table('employees')
            ->where('nik', $this->pic_proses_nik_daily)
            ->first();
        return $emp->nama ?? null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'Id_User', 'Id_User');
    }

    public function dailyPatrol()
    {
        return $this->belongsTo(DailyPatrol::class, 'Id_Daily_Patrol', 'Id_Daily_Patrol');
    }
}
