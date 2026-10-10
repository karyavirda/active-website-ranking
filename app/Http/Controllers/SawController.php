<?php

namespace App\Http\Controllers;

use App\Services\SawService;
use Illuminate\Http\Request;

class SawController extends Controller
{
    public function index()
    {
        return view('saw.index');
    }

    public function process(Request $request, SawService $saw)
    {
        $request->validate(['max_date' => 'required|date']);

        // Tanggal maksimal (J4) dari form
        $maxDateInput = $request->input('max_date');

        return view('saw.index', ['maxDateInput' => $maxDateInput] + $saw->calculate($maxDateInput));
    }
}
