<?php

if (! function_exists('currentEmpresaId')) {
    function currentEmpresaId(): int
    {
        return (int) session('empresa_id');
    }
}

if (! function_exists('currentEmpresa')) {
    function currentEmpresa(): ?\App\Models\Empresa
    {
        $id = currentEmpresaId();
        return $id ? \App\Models\Empresa::find($id) : null;
    }
}
