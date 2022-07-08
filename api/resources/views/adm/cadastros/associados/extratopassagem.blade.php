@extends('layouts.admin.template')

@section('title', $title)

@section('menu-top')
@stop

@section('menu-top-right')
@stop


@section('content-header')
    <section class="content-header">
    <h1>Associado</h1>
    <ol class="breadcrumb">
        <li> <a href="{{ route('admin.home') }}" >home</a></li>
        <li> <a href="{{ route('admin.home') }}" >Cadastros</a></li>
        <li> <a href="{{ route('admin.cadastros.associados') }}">Associados</a></li>
    </ol>
    </section>

@stop

@section('content')
    <section class="content">
        <div class="row text-right">
            <div class="col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12">
                <div class="info-box">
    
                @if($associado->saldopassagem<0)
                    <span class="info-box-icon bg-red"><i class="fa fa-ticket"></i></span>
                @elseif($associado->saldopassagem>0)
                    <span class="info-box-icon bg-blue"><i class="fa fa-ticket"></i></span>
                @else
                    <span class="info-box-icon bg-warning"><i class="fa fa-ticket"></i></span>
                @endif
                
                <div class="info-box-content">
                <span class="info-box-text">Seu saldo</span>
    
                @if($associado->saldopassagem<0)
                    <span class="info-box-number">{{$associado->saldopassagem * (-1) }} <small>tickets (débito)</small></span>
                @elseif($associado->saldopassagem>0)
                    <span class="info-box-number">{{$associado->saldopassagem }} <small>tickets</small></span>
                @else
                    <span class="info-box-number">Nenhum <small>ticket</small></span>
                @endif
    
                <div class="text-sm">em <small>{{$associado->saldodhupdate->format('d/m/y h:i') }}</small></div>
    
                
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
            </div>
            <!-- fix for small devices only -->
            <div class="clearfix visible-sm-block"></div>
    
        </div>
    
        <div class="box box-default">
            <div class="box-header with-border">
            <i class="fa fa-file-text" aria-hidden="true"></i> Extrato
            <div class="pull-right">
                <div class="btn-group">
                    <a href="{{route('admin.cadastros.associados.extrato', [$associado->id])}}" class="btn btn-default"><i class="fa fa-refresh" ></i> Últimos 90 dias</a>
                </div>
            </div>
            
            
            </div>
            <div class="box-body">


                <form class="form margin-bottom" action="{{route('admin.cadastros.associados.extrato', [$associado->id])}}" method="GET">
                    <div class="form-group col-xs-12 col-md-3">
                        <label for="txtdti">Mostrar desde</label>
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
                        
                
                <table class="table table-hover table-condensed table-borderless">
                    <tr class="hidden-xs">
                        <th>
                            <div class="col-md-1 col-sm-2 col-xs-12">Data</div>
                            <div class="col-md-8 col-sm-4 col-xs-12">Movimento</div>
                            <div class="col-md-1 col-sm-2 col-xs-3 text-right">Crédito</div>
                            <div class="col-md-1 col-sm-2 col-xs-3 text-right">Débito</div>
                            <div class="col-md-1 col-sm-2 col-xs-3 text-right">Saldo</div>
                        </th>
                    </tr>                
                @foreach ($extratos as $extrato)
                <tr class="{{$extrato->saldo<0 ? 'text-red text-bold' : 'text-black'}}"><td>
                        <div class="col-md-1 col-sm-3 col-xs-12 hidden-xs">{{ $extrato->datareg->format('d/m/y') }}</div>
                        <div class="col-md-1 col-sm-3 col-xs-12 visible-xs"><span class="text-bold">Data: </span>{{ $extrato->datareg->format('d/m/y') }}</div>
                        {{-- <div class="col-md-1 col-sm-1 col-xs-1">{{$extrato->tipo}}</div> --}}
                        {{-- <div class="col-md-1 col-sm-1 col-xs-1">{{$extrato->qtde}}</div> --}}
                        <div class="col-md-8 col-sm-6 col-xs-12">{!! utf8_encode(nl2br($extrato->descricao))!!}</div>
                        
                        <div class="col-md-1 col-sm-1 col-xs-6 text-right hidden-xs">{{$extrato->credito==0 ? '' : $extrato->credito}}</div>
                        <div class="col-md-1 col-sm-1 col-xs-6 text-right hidden-xs">{{$extrato->debito==0 ? '' : $extrato->debito}}</div>
    
                        @if(!$extrato->credito==0)
                        <div class="col-md-1 col-sm-1 col-xs-12 text-right visible-xs"><span class="text-bold">Crédito:</span> {{$extrato->credito==0 ? '' : $extrato->credito}}</div>
                        @endif
                            @if(!$extrato->debito==0)
                        <div class="col-md-1 col-sm-1 col-xs-12 text-right visible-xs"><span class="text-bold">Débito:</span> {{$extrato->debito==0 ? '' : $extrato->debito}}</div>                        
                        @endif
    
    
    
                        <div class="col-md-1 col-sm-1 col-xs-12 text-right hidden-xs">{{$extrato->saldo}}</div>
                        <div class="col-md-1 col-sm-1 col-xs-12 text-right visible-xs"><span class="text-bold">Saldo:</span> {{$extrato->saldo}}</div>
                    </td></tr>
                @endforeach
                </table>
    
    
        </div>
        </div>
    
    
        </section>
@stop