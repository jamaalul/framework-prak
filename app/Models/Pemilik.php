<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pemilik extends User
{
    use HasFactory;

    protected $table = 'pemilik';

    protected $primaryKey = 'idpemilik';

    protected $fillable = [
        'no_wa',
        'alamat',
        'iduser',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'iduser', 'iduser');
    }
}