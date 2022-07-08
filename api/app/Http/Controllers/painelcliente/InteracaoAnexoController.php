<?php

namespace App\Http\Controllers\painelcliente;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RetornoApiController;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\models\InteracaoAnexo;
use Illuminate\Support\Facades\Storage;

class InteracaoAnexoController extends Controller
{

    public function download($md5){
        $ret = new RetornoApiController;
        try {
            $md5 = isset($request->md5) ? $request->md5 : '';
            $arquivo = InteracaoAnexo::where('anexomd5', $md5)->first();
            if ($arquivo) {
                $filename = $arquivo->anexomd5 . "." . $arquivo->anexoext;
                $internal = 'arquivos/anexoschamados/' . $filename;
                $exists = Storage::disk('public')->exists($internal);
                if (!$exists) {
                    Storage::disk('public')->put($internal, $arquivo->anexo, 'public');
                }
                $ret->ok = true;
                $ret->rows = ['url' => Storage::disk('public')->get($internal)];
            } else {
                $ret->ok = false;
                $ret->msg = 'Arquivo não encontrado.';
            }
        } catch (Exception $e) {
            $ret->ok = false;
            $ret->msg = $e->getMessage();
        }
        return $ret->toJson();    
    }

}
