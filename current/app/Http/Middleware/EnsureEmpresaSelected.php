<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmpresaSelected
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('empresa_id')) {
            return redirect()->route('empresa.switch')->with('error', 'Selecciona una empresa.');
        }
        return $next($request);
    }
}
