<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function index()
    {
        $notes = Auth::user()->notes()
                      ->latest()
                      ->get();

        return view('library', compact('notes'));
    }
}
