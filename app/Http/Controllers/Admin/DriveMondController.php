<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DriveMondController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'DriveMond Controller is working']);
    }
}
