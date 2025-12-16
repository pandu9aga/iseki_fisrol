<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $table = 'nilais';
    protected $primaryKey = 'Id_Nilai';
    public $timestamps = false;

    protected $fillable = [
        'Id_Patrol',
        'Id_Temuan',
        'Id_Member',
        'Id_User',
        'Value_Nilai',
    ];

    public function patrol()
    {
        return $this->belongsTo(Patrol::class, 'Id_Patrol', 'Id_Patrol');
    }

    public function temuan()
    {
        return $this->belongsTo(Temuan::class, 'Id_Temuan', 'Id_Temuan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'Id_User', 'Id_User');
    }

    /**
     * Relasi ke member (database berbeda)
     * Tidak bisa eager-load via with(), tapi tetap bisa lazy-load per record.
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'Id_Member', 'id')
            ->select('id', 'nama');
    }

    public function getAuditorNameAttribute()
    {
        if ($this->member) {
            return $this->member->nama; // ambil dari employee/member
        }
        if ($this->user) {
            return $this->user->Name_User; // fallback ke user (admin)
        }
        return 'Auditor tidak dikenal';
    }
}
