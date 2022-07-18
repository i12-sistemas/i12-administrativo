<?php

namespace App\Http\Controllers\api\v1\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\models\Clientes;
use App\models\ClienteLicencai12;
use Exception;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\RetApiController;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClientesController extends Controller
{

  //GeraChave(CNPJ: WideString; TL, TS: Integer; DataEmissao: TDateTime): widestring;
  public function gerarlicenca($CNPJ, $TL, $TS, $DataEmissao)  {
    if (Str::length($CNPJ) < 14) throw new Exception('CNPJ Inválido');
    if (($TL < 1) || ($TL > 99)) throw new Exception('Tipo de licenciamento inválido');
    if (($TS < 1) || ($TS > 99)) throw new Exception('Tipo de sistema inválido');

    $grupo3 = Str::padLeft(random_int(1, 999999), 6, '0') . Str::padLeft(random_int(1, 9999999), 7, '0');
    $chave = Str::padLeft($TS, 2, '0') . $CNPJ . Str::padLeft($TL, 2, '0') . $DataEmissao->format('Ymd');
    $grupo1 = Str::substr($chave, 0, 13);
    $grupo2 = Str::substr($chave, 13, 13);

    // ----   PRIMEIRO GRUPO  ---------------------------
    $Sl = [];
    for ($i=13; $i >= 1 ; $i--) { 
      $temp2 = Str::substr($grupo3, $i-1, 1);
      $temp1 = Str::substr($grupo1, $i-1, 1);
      
      $temp1 = $temp2 . $temp1;
      $Sl[] = $temp1;
    }
    $Sl_HEX = [];
    foreach ($Sl as $value) {
      $n = intval($value);
      $x = dechex($n);
      $Sl_HEX[] = Str::padLeft($x, 2, '0') ;
    }
    $chave1 = implode(' ', $Sl_HEX);

    // ----   SEGUNDO GRUPO  ---------------------------
    $Sl = [];
    for ($i=1; $i <= 13 ; $i++) { 
      $temp2 = Str::substr($grupo3, $i-1, 1);
      $temp1 = Str::substr($grupo2, $i-1, 1);
      $temp1 = $temp2 . $temp1;
      $Sl[] = $temp1;
    }
    $Sl_HEX = [];
    foreach ($Sl as $value) {
      $n = intval($value);
      $x = dechex($n);
      $Sl_HEX[] = Str::padLeft($x, 2, '0') ;
    }
    $chave2 = implode(' ', $Sl_HEX);
    return Str::upper($chave1 . ' ' . $chave2);
  }

  public function gerar(Request $request)  {
    $ret = new RetApiController;
    try {    
      $empresa = session('empresa');
      if (!$empresa) throw new Exception('Nenhuma empresa logada.');
      if ($empresa->cnpj !== env('CNPJ_I12', '')) throw new Exception('Somente a empresa i12 Sistema pode fazer essa operação');
      
      $usuario = session('usuario');
      if (!$usuario) throw new Exception('Nenhum usuário logado');

      $modo = isset($request->modo) ? strVal($request->modo) : '';

      $dataemissao = null;
      if ($modo === 'vencimento') {
        try {
          $datavencimento = isset($request->data) ? strval($request->data) : null;
          if ($datavencimento ? $datavencimento === '' : true) throw new Exception('Formato inválido. Formato correto AAAA-MM-DD');
          
          $dataemissao = Carbon::createFromFormat('Y-m-d', $datavencimento)->subMonth(1);
          $datavencimento = Carbon::createFromFormat('Y-m-d', $datavencimento);
          $databloqueio = Carbon::createFromFormat('Y-m-d', $datavencimento->format('Y-m-d'))->addDay(10);
  
        } catch (\Throwable $th) {
          throw new Exception('Data de vencimento inválida - ' . $th->getMessage());
        }         

      } else if ($modo === 'bloqueio') {
        try {
          $databloqueio = isset($request->data) ? strval($request->data) : null;
          if ($databloqueio ? $databloqueio === '' : true) throw new Exception('Formato inválido. Formato correto AAAA-MM-DD');
          
          $datavencimento = Carbon::createFromFormat('Y-m-d', $databloqueio)->subDay(10);
          $dataemissao = Carbon::createFromFormat('Y-m-d', $datavencimento->format('Y-m-d'))->subMonth(1);
          $databloqueio = Carbon::createFromFormat('Y-m-d', $databloqueio);
  
        } catch (\Throwable $th) {
          throw new Exception('Data de bloqueio inválida - ' . $th->getMessage());
        }          
      } else {
        throw new Exception('Modo inválido. Informe por vencimento ou bloqueio');
      }

      $obs = isset($request->obs) ? strVal($request->obs) : null;
      if ($obs ? Str::length($obs) >= 500 : false) throw new Exception('Observação limitada a 500 caracteres');

      $cnpj = isset($request->cnpj) ? strVal($request->cnpj) : null;
      if ($cnpj ? Str::length($cnpj) < 11 : true) throw new Exception('CNPJ inválido');
      $cliente = Clientes::whereRaw('if(clientes.pessoa="F", concat("000",clientes.cpf),clientes.cnpj)=?', [$cnpj])->first();
      if (!$cliente) throw new Exception('Nenhum cliente identificado');
      if ($cliente->ativo !== 1) throw new Exception('Cliente inativo');
      
      $force = isset($request->cnpj) ? boolVal($request->force) : false;
      $check = ClienteLicencai12::where('atual', '=', 1)
                              ->where('idcliente', '=', $cliente->codcliente)
                              ->whereRaw('date(dhemissao) >= date(?)', [$dataemissao->format('Y-m-d')])
                              ->first();
      if ($check && !$force) 
        throw new Exception(Str::replaceArray('?',
                              [$check->dhemissao->format('d/m/Y'), $check->datavencimento->format('d/m/Y'), $check->databloqueiototal->format('d/m/Y')], 
                              'Já existe uma licença gerada com a data de emissão em ?, vencimento em ? e bloqueio em ?'));

      $tipolicenca = 1;
      $tiposistema = 1;
      $numero = $this->gerarlicenca($cnpj, $tipolicenca, $tiposistema, $dataemissao);

            
      try{
        DB::beginTransaction();

        ClienteLicencai12::where('atual', '<>', 0)
                  ->where('idcliente', '=', $cliente->codcliente)
                  ->update(['atual' => 0]);

        $registro = new ClienteLicencai12();
        $registro->dhemissao = $dataemissao;
        $registro->numero = $numero;
        $registro->tiposistema = $tiposistema;
        $registro->tipolicenca = $tipolicenca;
        $registro->atual = 1;
        $registro->obs = $obs;
        $registro->cnpj = $cnpj;
        $registro->idcliente = $cliente->codcliente;
        $registro->dhcad = Carbon::now();
        $registro->dhalt = Carbon::now();
        $registro->idusuario = $usuario->codusuario;
        $registro->idusuarioalt = $usuario->codusuario;
        $registro->save();


        ClienteLicencai12::where('atual', '=', 0)
                  ->where('idcliente', '=', $cliente->codcliente)
                  ->whereNull('dhativadaonline')
                  ->delete();

        DB::commit();
      } catch (Exception $e) {
          DB::rollBack();
          throw new Exception($e->getMessage());
      }    

      $ret->id = $registro->id;
      $ret->msg = 'Licença gerada com sucesso';
      $ret->ok=True;

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();
  }


  public function licencas(Request $request)  {
    $ret = new RetApiController;
    try {    
      $empresa = session('empresa');
      $find = isset($request->find) ? strval($request->find) : '';
      $status = isset($request->status) ? strval($request->status) : '';
      $perpage = isset($request->perpage) ? $request->perpage : 20;

      $dataset = Clientes::select('clientes.*', 
                                DB::raw('ifnull(cliente_saldo.saldovencido,0) as saldosaldovencido'),
                                DB::raw('cliente_saldo.vencidodesde as saldovencidodesde')
                                )
                  ->with('licencaatual')
                  ->where('clientes.ativo', '=', 1)
                  ->leftJoin('i12_clientelicenca', function($join){
                    return $join->on('clientes.codcliente', '=', 'i12_clientelicenca.idcliente')
                        ->where('i12_clientelicenca.atual', '=', 1);
                  })
                  ->leftJoin('cliente_saldo', function($join) use ($empresa){
                    return $join->on('clientes.codcliente', '=', 'cliente_saldo.clienteid')
                        ->where('cliente_saldo.empresaid', '=', $empresa->codempresa);
                  })
                  ->when($request->has('find') && ($find ? $find !== '' : false), function ($query) use ($find) {
                    return $query->where(function($query2) use ($find) {
                      $qLiked = '%' . $find . '%';
                      return $query2->where('clientes.nome', 'like', $qLiked)
                                ->orWhere('clientes.fantasia', 'like', $qLiked);
                    });
                  })
                  ->when($request->has('status') && ($status ? $status !== '' : false), function ($query) use ($status) {
                    if ($status === 'bloqueado') {
                      return $query->whereRaw('(date(date_add(date(date_add(i12_clientelicenca.dhemissao, interval i12_clientelicenca.tipolicenca MONTH)), interval 10 day)) < date(now()) or (i12_clientelicenca.id is null)) ');
                    } else if ($status === 'expirado') {
                      return $query->whereRaw('(date(date_add(i12_clientelicenca.dhemissao, interval i12_clientelicenca.tipolicenca MONTH)) < date(now()) or (i12_clientelicenca.id is null))');
                    } else if ($status === 'naoaplicado') {
                      return $query->whereRaw('i12_clientelicenca.dhativadaonline is null');
                    } else if ($status === 'semlicenca') {
                      return $query->whereRaw('i12_clientelicenca.id is null');
                    }
                  })
                  ->orderBy('clientes.nome')
                  ->groupBy('clientes.codcliente')
                  ->paginate($perpage);
      $dados = [];
      foreach ($dataset as $key => $cliente) {
        $d = [
          'cliente' => [
              'id' => $cliente->codcliente,
              'nome' => $cliente->nome,
              'doc' => $cliente->pessoa === 'F' ? $cliente->cpf : $cliente->cnpj,
              'ativo' => $cliente->ativo,
              'saldovencido' => $cliente->saldosaldovencido,
              'saldovencidodesde' => $cliente->saldovencidodesde ? $cliente->saldovencidodesde->format('Y-m-d') : null,
            ]
        ];
        if ($cliente->licencaatual) {
          $licenca = $cliente->licencaatual;

          $d['licencaatual'] = [
            'numero' => $licenca->numero,
            'datavencimento' => $licenca->datavencimento ? $licenca->datavencimento->format('Y-m-d') : null,
            'expirado' => $licenca->expirado,
            'databloqueio' => $licenca->databloqueiototal ? $licenca->databloqueiototal->format('Y-m-d') : null,
            'bloqueado' => $licenca->bloqueado,
            'status' => $licenca->status,
            'diasrestantes' => $licenca->diasrestantes,
            'dataativacao' => $licenca->dhativadaonline ? $licenca->dhativadaonline->format('Y-m-d H:i:s') : null,
          ];

        }
        $dados[] = $d;
      }

      $ret->collection=$dataset;
      $ret->data=$dados;
      $ret->ok=True;

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();
  }
}
