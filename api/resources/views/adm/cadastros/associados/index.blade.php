@extends('layouts.admin.template')

@section('title', $title)

@section('menu-top')
@stop

@section('menu-top-right')                    
@stop


@section('content-header')
    <section class="content-header">
    <h1><small>Olá, </small> {{ Auth::guard('admin')->User()->nome }}</h1>
    <ol class="breadcrumb">
        <li> <a href="route('admin.home')" >home</a></li>
        <li> <a href="#" >Cadastros</a></li>
        <li class="active">Associados</li>
    </ol>
    </section>

@stop

@section('content')
<section class="content">
<div class="row">
    <div class="col-md-12">
    <div class="box">
    <div class="box-header">
        <span>Filtrar</span>
        <span>Status: <b>Ativos</b></span>    
        <span> | </span>    
        
        <a href="{{route('admin.cadastros.associados.show', 'novo')}}" class="btn btn-default btn-sm" ><i class="fa fa-plus"></i> Novo cadastro</a>

        <div class="box-tools pull-right">
       
            <form action="{{ route('admin.cadastros.associados') }}" method="GET">
            <div class="input-group input-group-sm" style="width: 150px;">
                <input type="search" name="search" class="form-control pull-right" placeholder="Consultar..." value="{{ $search }}">
                <div class="input-group-btn">
                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                </div>
            </div>
            <form>
        </div>
    </div>    
    <div class="box-body">
        <table class="table table-condensed table-hover">
            <tbody>
                @foreach ($associados as $associado)
                <tr>
                    <td>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="col-xs-2"><img src="{{$associado->fotourlsmall }}" class="img-thumbnail img-responsive" /></div>
                            <div class="col-xs-8">
                                <h4>{{$associado->nome}}</h4>
                                <div>Matrícula: {{$associado->id}}</div>
                                <div>Curso: {{$associado->curso}} - {{$associado->instituicaoensinocidade}}</div>
                                @if($associado->saldopassagem<0)
                                    <div class="h3 text-red">Saldo: <strong>{{$associado->saldopassagem }} (débito)</strong><small> ticket(s) - {{$associado->saldodhupdate->format('d/m/Y h:i') }} </small></div>
                                @elseif($associado->saldopassagem==0)
                                    <div class="h3 text-black">Saldo: Nenhum ticket - {{$associado->saldodhupdate->format('d/m/Y h:i') }} </small></div>
                                @else
                                    <div class="h3 text-blue">Saldo: <strong>{{$associado->saldopassagem }}</strong><small> ticket(s) - {{$associado->saldodhupdate->format('d/m/Y h:i') }} </small></div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-2">
                            <a href="{{route('admin.protocolos.index', $associado->id)}}" class="btn btn-md {{ $associado->protocolosabertos->count()>0 ? 'btn-info' : 'btn-default' }}" ><i class="fa fa-comment"></i> {{ $associado->protocolosabertos->count() }}</a>
                            <a href="{{route('admin.cadastros.associados.show', $associado->id)}}" class="btn btn-default btn-md" ><i class="fa fa-edit"></i></a>
                            <a href="{{route('admin.cadastros.associados.carteirinha', $associado->id)}}" target="_blank" class="btn btn-default btn-md" ><i class="fa fa-id-card-o"></i></a>
                            <a href="{{route('admin.cadastros.associados.extrato', $associado->id)}}" target="_self" title="Extrato de passagem" class="btn btn-default btn-md" ><i class="fa fa-ticket"></i></a>
                        </div>
                    </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>                
        <div class="text-center">{{ $associados->links() }}</div>

    </div>
    </div>
    </div>
</div>
</section>
@stop