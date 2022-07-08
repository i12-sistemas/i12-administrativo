<?php

namespace App\Http\Controllers\API\v1\admin\cadastro;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\RetornoApiController;
use App\models\FaturaFormapgto;

class FaturaFormapgtoController extends Controller
{
    function list(Request $request) {

        $ret = new RetornoApiController;
        try {
            $ativo = (isset($request->ativo) ? intval($request->ativo) : 1);
            $formas = FaturaFormapgto::where('ativo', $ativo)
                ->orderBy('forma', 'asc')
                ->get();

            $ret->ok = true;
            $ret->rows = $formas;
            return $ret->toJson();

        } catch (Exception $e) {
            $ret->ok = false;
            $ret->msg = $e->getMessage();
            return $ret->toJson();
        }

    }
}
