<?php

namespace App\Http\Controllers\API\v1\empresas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;


//CONTROLLER DA API

use App\Http\Controllers\RetornoApiController;

use App\models\i12LogErro;
use App\models\Clientes;

class i12LogErrosController extends Controller
{
    
    protected $dir = '';
    protected $storagedisk = 's3';
  
    public function __construct () {
      $this->dir = env('STORAGE_DIR_LOGERRO','');
      $this->storagedisk = ENV('STORAGE_MODO', 's3');
    }
    
    // Salva no banco de dados os registros vindos do iCom dos clientes.

    public function store(Request $request)
    {
        $ret = new RetornoApiController;
        try {
            $rules = [
                'idlocal'      => ['required', 'integer'],
                'datahora'      => ['required', 'date'],
                'cnpj'          => ['required', 'min:14', 'max:14'],
                'versaoapp'     => ['required', 'min:3'],
                'moduloapp'     => ['required', 'min:3'],
                'versaobd'      => ['required', 'min:3'],

            ];
            $messages = [
                'required' => 'Campo :attribute é obrigatório!',
                'date' => 'Formato de Data inválido!',
                'max' => 'O campo :attribute, deverá ter no máximo :max caracteres!',
                'min' => 'O campo :attribute, deverá ter no mínimo :min caracteres!',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $msgs = [];
                $errors = $validator->errors();
                foreach ($errors->all() as $message) {
                    $msgs[] = $message;
                }
                $ret->data = $msgs;
                throw new Exception(join("; ", $msgs));
            }

            $erro = i12LogErro::where('cnpj', $request->cnpj)->where('idlocal', $request->idlocal)->first();
            if ($erro) {
              $ret->ok = true;
              throw new Exception('Registro já foi inserido anteriormente');
            }

            //Verifica se o campo printscreen está no cabeçalho da requisição
            $printscreen =  isset($request->printscreen) ? $request->printscreen : null;
            // Verifica se o valor do campo é válido, caso contrário, envia null
            if (strlen($printscreen) < 5) $printscreen = null;

            // Se a variável é válida
            if ($printscreen) {
                //Tratamento de Imagem base64 - Decode String
                // ENCODE -> Converte uma IMAGEM em STRING
                // DECODE -> Converse uma STRING em IMAGEM
                $anexo = base64_decode($printscreen);
                $md5 = md5($anexo);
                $extension = getExtFileBase64($anexo);

                $dir  = $this->dir . '/' . $request->cnpj;
                $name  = $md5 . '.' . $extension;
                $filename = $dir . '/' . $name;


             //Declara que a imagem será salva na pasta publica (app/public) **usar "php artisan storage::link" para criar link publico acessível   
                $disk = \Storage::disk($this->storagedisk);
             // Salva o arquivo e as informações na variável DISK   
                $disk->put($filename, $anexo, 'public');
                if (!$disk->exists($filename)) throw new Exception('Falha ao realizar upload do arquivo!');
                $size = $disk->size($filename);
                $urldownload = $disk->url($filename);
            }

        } catch (Exception $e) {
            $ret->msg = $e->getMessage();
            return $ret->toJson();
        }    

        try{
            // Inicia transação no banco
            DB::beginTransaction();     

            $registrolog = new i12LogErro;
            $registrolog->idlocal           = $request->idlocal;
            $registrolog->datahora          = $request->datahora;
            $registrolog->problema          = $request->problema;
            $registrolog->cnpj              = $request->cnpj;
            $registrolog->versaoapp         = $request->versaoapp;
            $registrolog->moduloapp         = $request->moduloapp;
            $registrolog->versaobd          = $request->versaobd;
            $registrolog->formulario        = $request->formulario;
            $registrolog->controle          = $request->controle;
            $registrolog->tipo              = $request->tipo;
            $registrolog->usuarioso         = $request->usuarioso;
            $registrolog->usuariosistema    = $request->usuariosistema;
            $registrolog->nomecomputador    = $request->nomecomputador;
            $registrolog->so                = $request->so;
            $registrolog->releaseapp        = $request->releaseapp;
            $registrolog->md5app            = $request->md5app;
            $registrolog->ip            = $request->ip();
            // Caso a variável de anexo for válida, salva as informações da imagem.
            if ($printscreen) {
              $registrolog->filename      = $filename;
              $registrolog->size          = $size;
              $registrolog->filenameurl   = $urldownload;
            }

            $saved = $registrolog->save();
            if (!$saved){
                throw new Exception('Falha ao salvar Log!');
            }
            DB::commit();

            $ret->ok = true;
        } catch (Exception $e) {
            DB::rollBack();
            $ret->msg = $e->getMessage();
            return $ret->toJson();
        }        
        return $ret->toJson();
    }
    
}
