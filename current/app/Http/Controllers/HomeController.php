<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function empresa()
    {
        $empresas = Empresa::orderBy('id')->get();
        return view('empresa.switch', compact('empresas'));
    }

    public function empresaSet(Request $request)
    {
        $data = $request->validate([
            'empresa_id' => ['required','integer','exists:empresas,id'],
        ]);

        $e = Empresa::findOrFail($data['empresa_id']);

        session([
            'empresa_id' => $e->id,
            'empresa_nombre' => $e->nombre ?? ('Empresa #'.$e->id),
        ]);

        return redirect()->route('dashboard');
    }
}
