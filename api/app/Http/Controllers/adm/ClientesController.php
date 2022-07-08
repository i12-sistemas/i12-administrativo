<?php

namespace App\Http\Controllers\adm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\models\Clientes;
use Exception;

class ClientesController extends Controller
{
  public function licencas(Request $request)  {
    $q = $request->q;
    $qLiked = '';
    if ($q != '') $qLiked = '%' . $q . '%';
    if ($qLiked == '') $qLiked = null;

    $vencidos = isset($request->vencidos) ? $request->vencidos : null;
    $vencidos = ($vencidos == 'on') ;
    if (!$vencidos) $vencidos = null;

    $clientes = Clientes::where('clientes.ativo', '=', 1)
                        ->when($qLiked, function ($query, $qLiked) {
                          return $query->where('clientes.nome', 'like', $qLiked)
                                    ->orWhere('clientes.fantasia', 'like', $qLiked);
                        })
                        ->when($vencidos, function ($query, $vencidos) {
                          return $query->join('i12_clientelicenca', function($join){
                            return $join->on('clientes.codcliente', '=', 'i12_clientelicenca.idcliente')
                                 ->where('i12_clientelicenca.atual', '=', 1)
                                 ->whereRaw('(date_add(i12_clientelicenca.dhemissao, interval i12_clientelicenca.tipolicenca MONTH)) < date(now())');
                          });
                        })
                        ->orderBy('clientes.nome')
                        ->groupBy('clientes.codcliente')
                        ->paginate(15)
                        ->appends(request()->query());
    return view('adm.clienteslicencas', compact('clientes', 'q'));
  }
}
