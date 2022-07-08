<?php

namespace App\Http\Controllers\API\v1\empresas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RetornoApiController;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\models\ClienteLicencai12;

class LicencasController extends Controller
{
    public function find(Request $request) {

        $ret = new RetornoApiController;
        try{
            $cnpj = isset($request->cnpj) ? $request->cnpj : '';

            if($cnpj=='') throw new Exception('Nenhum CNPJ informado.');
            $cnpjs = explode(',', $cnpj);
            $cnpjvalido = [];
            foreach($cnpjs as $cnpj){
                $cnpj = cleancnpjcpf($cnpj);
                $cnpjvalido[]  =$cnpj;
            }
            $cnpjvalido = array_unique($cnpjvalido);
            

            $licencas = ClienteLicencai12::select('i12_clientelicenca.*')
                            ->where('i12_clientelicenca.atual', '=', 1)
                            ->whereRaw('if(clientes.pessoa="J", find_in_set(clientes.cnpj, ?), find_in_set(concat("000",clientes.cpf), ?))', [implode(',',$cnpjvalido), implode(',',$cnpjvalido)])
                            ->join('clientes', 'i12_clientelicenca.idcliente', '=', 'clientes.codcliente')
                            ->get();
            $dados = [];
            foreach ($licencas as $lic) {
              $dados[] = [
                'id'              =>  $lic->id,
                'dataemissao'     =>  $lic->dhemissao->format('Y-m-d'),
                'datavencimento'  =>  $lic->datavencimento->format('Y-m-d'),
                'licenca'         =>  $lic->numero,
                'cnpj'            =>  $lic->cnpj,
                'tl'              =>  $lic->tipolicenca,
                'ts'              =>  $lic->tiposistema,
              ];
            };
            
            // $sql = 'select i12_clientelicenca.id, cast(i12_clientelicenca.dhemissao as date) as DataEmissao, 
            //         cast((i12_clientelicenca.dhemissao + interval i12_clientelicenca.tipolicenca month) as date) AS DataVencimento,
            //         i12_clientelicenca.numero as Licenca, i12_clientelicenca.cnpj
            //         from i12_clientelicenca 
            //         inner join clientes ON clientes.codcliente = i12_clientelicenca.idcliente
            //         where i12_clientelicenca.atual = 1 
            //         AND find_in_set(clientes.cnpj, ?) 
            //         order by (i12_clientelicenca.dhemissao + interval i12_clientelicenca.tipolicenca month) desc , i12_clientelicenca.dhemissao desc';
            // $licencas = DB::connection('mysql')->select($sql, [implode(',',$cnpjvalido)]);

            // $sql = 'select licenca.ID, cast(licenca.DataEmissao as date) as DataEmissao, 
            //             cast((licenca.DataEmissao + interval licenca.TL month) as date) AS DataVencimento,
            //             licenca.Licenca, empresa.cnpj
            //             from licenca inner join empresa ON empresa.ID = licenca.idempresa
            //             where licenca.Atual = 1 AND find_in_set(empresa.cnpj, ?) 
            //             order by (licenca.DataEmissao + interval licenca.TL month) desc , licenca.DataEmissao desc';
           
            
            $ret->ok = true;
            $ret->rows = $dados;
        } catch (Exception $e) {
            $ret->msg = $e->getMessage();
        }

        return $ret->toJson();
    }

    public function setApply(Request $request) {

        $ret = new RetornoApiController;
        try{
            $user = isset($request->user) ? $request->user : '';
            $useremail = isset($request->useremail) ? $request->useremail : '';
            $modo = isset($request->modo) ? $request->modo : '';
            $ids = isset($request->ids) ? $request->ids : '';

            if($ids=='') throw new Exception('Nenhum ID informado.');
            $ids = explode(',', $ids);
            $idsvalido = [];
            foreach($ids as $id){
                $id = intval($id);
                if($id>0) $idsvalido[] = $id;
            }
            $idsvalido = array_unique($idsvalido);

        } catch (Exception $e) {
            $ret->msg = $e->getMessage();
        }


        try{
            DB::beginTransaction();
            foreach ($idsvalido as $id) {
              ClienteLicencai12::where('id', $id)->update(['dhativadaonline' => Carbon::now()]);
            }
            DB::commit();
            $ret->ok = true;
        } catch (Exception $e) {
            DB::rollBack();
            $ret->msg = $e->getMessage();
        }

        return $ret->toJson();
    }    
}
