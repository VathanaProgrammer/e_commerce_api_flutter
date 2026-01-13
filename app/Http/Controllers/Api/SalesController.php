<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    //
    public function test(Request $r){
        Log::info('Data', ['data' => $r->all()]);
    }
}