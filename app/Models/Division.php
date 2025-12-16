<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $connection = 'rifa';
    protected $table = 'divisions'; // Nama tabel
    protected $primaryKey = 'id'; // Nama primary key

    protected $fillable = ['nama'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
