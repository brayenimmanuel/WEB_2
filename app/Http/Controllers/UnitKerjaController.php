<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitKerja;

class UnitKerjaController extends Controller
{
    public function index()
    {
        $units = UnitKerja::all();
        return view('unit-kerja.index', compact('units'));
    }
}
