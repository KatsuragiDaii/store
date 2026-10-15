<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function pengiriman()
    {
        return view('pages.pengiriman');
    }

    public function retur()
    {
        return view('pages.retur');
    }

    public function kontak()
    {
        return view('pages.kontak');
    }
}
