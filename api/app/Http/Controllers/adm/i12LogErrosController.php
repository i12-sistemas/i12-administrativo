<?php

// namespace App\Http\Controllers\API\v1\empresas;
namespace App\Http\Controllers\adm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\DB;
// use Carbon\Carbon;
use Exception;

use App\models\i12LogErro;
use App\models\Clientes;

// CONTROLLER DAS ROTAS WEB PARA LISTAGEM E CONSULTAS
class i12LogErrosController extends Controller
{
    public function listaclientes(Request $request)  {
        $q = $request->q;
        $qLiked = '';
        if ($q != '') $qLiked = '%' . $q . '%';
        if ($qLiked == '') $qLiked = null;   
        
        $clientes = Clientes::where('clientes.ativo', '=', 1)
        ->when($qLiked, function ($query, $qLiked) {
            return $query->where('clientes.nome', 'like', $qLiked)
            ->orWhere('clientes.fantasia', 'like', $qLiked);
        })
        ->orderBy('clientes.nome')
        ->groupBy('clientes.codcliente')
        ->paginate(15)
        ->appends(request()->query());      
        
        return view('adm.clienteslogerros.listagem', compact('clientes', 'q'));
    }
    
    public function logsclientes($cnpj){ 
        
        try{
            $logs  = i12LogErro::orderBy('id')
            ->where('cnpj', $cnpj)
            ->paginate(10);
            $cliente = Clientes::where('cnpj', $cnpj);
            // dd($cliente);
            return view('adm.clienteslogerros.logs',compact('logs', 'cliente'));
            
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
            exit();
        }
    }

    public function index(Request $request){ 
        
      try{
          $logs  = i12LogErro::orderBy('datahora', 'desc')->paginate(50);
          return view('adm.clienteslogerros.logall',compact('logs'));
          
      } catch (Exception $e) {
          return redirect()->back()->with('error', $e->getMessage());
          exit();
      }
  }

}
