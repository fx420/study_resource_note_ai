<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Note;

class LibraryController extends Controller
{
    public function index()
    {
        $notes = Note::where('user_id', Auth::id())
                     ->latest()
                     ->get();
        return view('library.library', compact('notes'));
    }
}
