<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Perawat extends User
{
    use HasFactory;

    protected $table = 'perawat';

    protected $primaryKey = 'id_perawat';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'alamat',
        'no_hp',
        'jenis_kelamin',
        'pendidikan',
        'id_user',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'iduser');
    }
}
