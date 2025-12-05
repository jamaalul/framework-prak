<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekamMedis;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RekamMedisController extends Controller
{
    public function show($id)
    {
        $user = User::with('role')->find(Auth::id());
        
        $rekamMedis = RekamMedis::with(['pet.pemilik.user', 'detailRekamMedis.kodeTindakanTerapi'])
            ->findOrFail($id);

        return view('rekam_medis.show', [
            'user' => $user,
            'rekamMedis' => $rekamMedis,
        ]);
    }
}
