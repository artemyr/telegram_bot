<?php

namespace App\Http\Controllers;

class WorkController extends Controller
{
    public function config()
    {
        return response()->json([
            'start' => '123'
        ]);
    }
}
