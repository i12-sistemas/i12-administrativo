<?php

namespace App\Http\Controllers\painelcliente;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\painelcliente\Auth\LoginController;
use App\models\OrdemServico;

class SpaController extends Controller
{
    public function index()
    {
        return view('painelcliente.spa');
    }

    public function linkpublicopainel($idbase64)
    {
      
      $meta_description = 'Chamado não identificado';
      $meta_title = 'Chamado não identificado';
      if(Auth::guard('painelcliente')->getSession()->has('cliente')){
        Auth::guard('painelcliente')->getSession()->forget('cliente');
        Auth::guard('painelcliente')->getSession()->flush();
      }
      Auth::guard('painelcliente')->logout();
      
      $id = base64_decode($idbase64);
      $chamado = OrdemServico::where('osordem.modo', 'OS')->where('id', $id)->first();
      if($chamado){
        $meta_title = 'Chamado #' . $chamado->id . ' - ' . $chamado->cliente->nome;
        $problema =  $chamado->problemareclamado;
        $problema = str_replace(array("\r\n","\r","\n"), "<br>",$problema);
        $meta_description = 'Aberto em ' . $chamado->dtabertura->format('d/m/Y') . ' - ' . $problema;
      }
      return view('painelcliente.spa', compact('meta_title', 'meta_description'));
    }    
}
