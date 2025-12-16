<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $connection = 'rifa';
    protected $table = 'employees'; // Nama tabel di DB rifa
    protected $primaryKey = 'id';   // Primary key tabel
    public $timestamps = false;     // <- tambahkan ini

    protected $fillable = [
        'nama',
        'nik',
        'team',
        'division_id',
        'status'
    ];

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'id');
    }

    // app/Models/Member.php

    public function getDisplayNameAttribute()
    {
        return $this->nama;
    }
}
