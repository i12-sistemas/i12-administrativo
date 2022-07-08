@extends('layouts.admin.template')

@section('title', $title)

@section('menu-top')
@stop

@section('menu-top-right')                    
@stop

@section('content-header')
<section class="content-header">
    <h1>Consulta de Vendas</h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.home')}}" >Home</a></li>
        <li><a href="{{route('admin.operacional.vendas')}}" >Vendas</a></li>
        <li class="active">Consultar</li>
        <li> <a href="{{route('admin.operacional.vendas.mensal.novo')}}" >Nova venda</a></li>
    </ol>


        
@stop

@section('content')
<section class="content">
    
<div class="row">
    <div class="col-sx-12 col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Vendas</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">

                <form class="form margin-bottom" action="{{route('admin.operacional.vendas.consulta')}}" method="GET">
                    <div class="form-group col-xs-12 col-md-4">
                        <label for="txtsearch">Consultar</label>
                        <input type="search" id="txtsearch" name="str" class="form-control" placeholder="Consultar por associado" value="{{ Request::get('str') }}" />
                    </div>                                          
                    <div class="form-group col-xs-12 col-md-3">
                        <label for="txtdti">Período</label>
                        <input type="date" id="txtdti" name="dti" class="form-control" value="{{ Request::get('dti') }}"/>
                    </div>
                    <div class="form-group col-xs-12 col-md-3">
                        <label for="txtdtf">até</label>
                        <input type="date" id="txtdtf" name="dtf" class="form-control" value="{{ Request::get('dtf') }}" />
                    </div>    
                    
                    <div class="form-group  col-xs-12 col-md-2">
                        <button type="submit" class="btn btn-app btn-sm" ><i class="fa fa-search"></i> Consultar</button>
                    </div>                                        
                </form>

                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>
                                <div class="row">
                                    <div class="col-md-1">Nº venda</div>
                                    <div class="col-md-2">Data</div>
                                    <div class="col-md-3">Associado e observações</div>
                                    <div class="col-md-2 text-center">Qtde/Usado</div>
                                    <div class="col-md-2 text-right">Total</div>
                                    <div class="col-md-2 text-right">opções</div>
                                </div>                        
                            </th>
                        </tr>
                        @foreach ($vendas as $venda)
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-md-1">
                                        @if($venda->status==0)
                                            <span class="text-danger"><i class="fa fa-warning"></i></span>
                                        @else
                                            <span class="text-primary"><i class="fa fa-check-circle-o"></i></span>
                                        @endif                            
                                        {{ $venda->id }}
                                    </div>
                                    <div class="col-md-2">{{$venda->dhregistro->format('d/m/Y h:i')}}</div>
                                    <div class="col-md-3">
                                        <div>{{$venda->associado->nome}}</div>
                                        @if($venda->obs<>'')
                                            <div>{{$venda->obs}}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <div class="text-center">{{ ($venda->qtdetotal + $venda->vendasfilho->sum('qtdetotal'))  . ' / ' . ($venda->qtdebaixa + $venda->vendasfilho->sum('qtdebaixa'))   }}</div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="text-right">{{formatRS($venda->total + $venda->vendasfilho->sum('total'))}}</div>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <div class="btn-group">
                                            <a href="{{route('admin.operacional.vendas.show', [$venda->id])}}"  title="Detalhar" class="btn btn-default btn-sm"><i class="fa fa-search"></i></a>
                                            <a href="{{route('admin.operacional.vendas.print', [$venda->id])}}" target="_blank" title="Imprimir venda" class="btn btn-default btn-sm"><i class="fa fa-print"></i></a>
                                        </div>                                
                                        
                                    </div>
                                </div>

                                @if($venda->vendasfilho->count()>0)
                                <div class="row">
                                    <div class="col-md-11 col-md-offset-1">
                                @foreach ($venda->vendasfilho as $filho)
                                    <a href="{{route('admin.operacional.vendas.show', [$filho->id])}}" target="_blank" title="Imprimir venda (filho)" class="btn btn-default btn-sm">
                                        @if($filho->status==0)
                                            <span class="text-danger"><i class="fa fa-warning"></i>
                                        @else
                                            <span class="text-primary"><i class="fa fa-check-circle-o"></i>
                                        @endif                                
                                        #{{$filho->id}}{{$filho->obs=='' ? '' : ' - ' . $filho->obs}}
                                            </span>

                                    </a>
                                @endforeach                        
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <div class="text-center">{{ $vendas->links() }}</div>
                            </td>
                        </tr>
                    </tfoot>
            
                </table>
            </div>

            <div class="box-footer">
                <div class="col-sx-12 small text-right">
                    <span class="text-danger margin-r-5"><i class="fa fa-warning"></i> Venda bloqueada (em análise financeira)</span>
                    <span class="text-primary"><i class="fa fa-check-circle-o"></i> Venda liberada</span>
                </div>    
            </div>
        </div>        
    </div>
</div>
</section>
@stop

