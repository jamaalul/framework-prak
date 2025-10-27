<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userWithRoles = User::with('role')->find(Auth::id());

        $modelName = $request->get('model', 'JenisHewan');
        $modelClass = "App\\Models\\{$modelName}";

        $relationships = [];
        $columns = [];

        switch ($modelName) {
            case 'User':
                $relationships = ['role'];
                $columns = ['nama', 'email', 'roles'];
                break;
            case 'JenisHewan':
                $relationships = ['rasHewan'];
                $columns = ['nama_jenis_hewan', 'ras_hewan'];
                break;
            case 'RasHewan':
                $relationships = ['jenisHewan'];
                $columns = ['nama_ras', 'jenis_hewan'];
                break;
            case 'KodeTindakanTerapi':
                $relationships = ['kategori', 'kategoriKlinis'];
                $columns = ['kode', 'deskripsi_tindakan_terapi', 'kategori', 'kategori_klinis'];
                break;
            case 'Pet':
                $relationships = ['pemilik.user', 'rasHewan'];
                $columns = ['nama', 'tanggal_lahir', 'warna_tanda', 'jenis_kelamin', 'pemilik', 'ras_hewan'];
                break;
            default:
                $columns = (new $modelClass)->getFillable();
                break;
        }

        return view('dashboard.index', [
            'user' => $userWithRoles,
            'model' => $modelClass,
            'relationships' => $relationships,
            'columns' => $columns,
            'title' => $modelName
        ]);
    }
}
