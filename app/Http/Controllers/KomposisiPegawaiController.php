<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Services\SdmIndikatorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KomposisiPegawaiController extends Controller
{
    public function index(Request $request, Division $division): View
    {
        abort_unless($division->slug === 'sdm', 404);

        $service = app(SdmIndikatorService::class);

        return view('divisi.sdm.komposisi', [
            'division' => $division,
            'komposisi' => $service->komposisiSdm(),
        ]);
    }
}
