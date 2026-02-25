<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Temuan extends Model
{
    protected $table = 'temuans';
    protected $primaryKey = 'Id_Temuan';
    public $timestamps = false;

    protected $fillable = [
        'Path_Temuan',
        'Desc_Temuan',
        'Path_Update_Temuan',
        'Desc_Update_Temuan',
        'Id_Patrol',
        'Id_User',
        'Id_Member',
        'nik_penemu',
        'Status_Temuan',
        'pic_proses_nik',
        'Rotate_Temuan',
        'Rotate_Update'
    ];

    /**
     * Get penemu name from rifa.employees via nik_penemu
     */
    public function getNikPenemuNameAttribute()
    {
        if (!$this->nik_penemu) return null;
        $emp = DB::connection('rifa')
            ->table('employees')
            ->where('nik', $this->nik_penemu)
            ->first();
        return $emp->nama ?? null;
    }

    /**
     * Get PIC Proses name from rifa.employees via pic_proses_nik
     */
    public function getPicProsesNameAttribute()
    {
        if (!$this->pic_proses_nik) return null;
        $emp = DB::connection('rifa')
            ->table('employees')
            ->where('nik', $this->pic_proses_nik)
            ->first();
        return $emp->nama ?? null;
    }

    public function getPenemuNameAttribute()
    {
        if ($this->member) {
            return $this->member->nama;
        }

        if ($this->user) {
            return $this->user->Name_User; // admin input
        }

        return '-';
    }
    // Relasi ke user (admin)
    public function user()
    {
        return $this->belongsTo(User::class, 'Id_User', 'Id_User');
    }

    // Relasi ke member (penemu)
    public function member()
    {
        return $this->belongsTo(Member::class, 'Id_Member', 'id');
    }

    // Relasi ke patrol
    public function patrol()
    {
        return $this->belongsTo(Patrol::class, 'Id_Patrol', 'Id_Patrol');
    }

    public function patrolMember()
    {
        return $this->hasOne(PatrolMember::class, 'Id_Patrol', 'Id_Patrol')
            ->whereColumn('patrol_members.Id_User', 'Id_User');
    }

    public function getNamaMemberAttribute()
    {
        $member = DB::connection('rifa')
            ->table('employees')
            ->where('id', $this->Id_Member)
            ->first();

        return $member->nama ?? '-';
    }
}
