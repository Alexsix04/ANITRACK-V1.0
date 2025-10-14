<?php

namespace App\Http\Controllers;

abstract class AnimeController extends Controller
{
    public function index()
    {
        $animes = \App\Models\Anime::latest()->take(20)->get();
        return view('animes.index', compact('animes'));
    }
}