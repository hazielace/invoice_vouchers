<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseCheckController extends Controller
{
    public function checkConnection()
    {
        try {
            DB::connection()->getPdo();
            return response()->json(['status' => 'success', 'message' => 'Conexion exitosa.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Conexion fallida: ' . $e->getMessage()], 500);
        }
    }
}
