<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dokter extends User
{
    use HasFactory;

    protected $table = 'dokter';

    protected $primaryKey = 'id_dokter';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'alamat',
        'no_hp',
        'bidang_dokter',
        'jenis_kelamin',
        'id_user',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'iduser');
    }
}
