<?php

namespace App\Http\Controllers;

use App\Models\Cleaning;
use App\Models\User;
use Illuminate\Http\Request;

class CleanerController extends Controller
{
    public function index()
    {
        $cleaner = User::where('role_id', 6)->get();
        return $cleaner;
    }
}
