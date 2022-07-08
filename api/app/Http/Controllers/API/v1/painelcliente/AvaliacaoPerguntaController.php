<?php

namespace App\Http\Controllers\API\v1\painelcliente;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RetornoApiController;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\models\AvaliacaoPergunta;

class AvaliacaoPerguntaController extends Controller
{

    public function list(Request $request) {
        
        $ret = new RetornoApiController;
        try{
            $tipo = isset($request->tipo) ? $request->tipo : 'OS';

            $perguntas = AvaliacaoPergunta::where('localaplicacao', $tipo)
                            ->where('ativo', 1)
                            ->orderBy('ordem')
                            ->get();

            $ret->ok = true;
            $ret->rows = $perguntas;
        } catch (Exception $e) {
            $ret->msg = $e->getMessage();
        }

        return $ret->toJson();
    }

}
