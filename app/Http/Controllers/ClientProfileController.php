<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientProfileController extends Controller
{
    public function show(Request $request)
    {
        //devuelve el usuario autetinticado
        $user = $request->user();

        //devuelve la info del usuario
        return response()->json($user);
    }
}
