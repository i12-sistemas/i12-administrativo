@extends('painelcliente.layout.template')

@section('title', 'Área do usuário')

@section('menu-top')
@stop

@section('menu-top-right')                    
@stop


@section('content-header')
    <section class="content-header">
    <h1>
        <small>Olá, </small> {{ Auth::guard('painelcliente')->User()->nome . ' (' .  Auth::guard('painelcliente')->getSession()->get('cliente')->fantasia . ')'  }}
        
    </h1>
    <ol class="breadcrumb">
        <li class="active">home</li>
    </ol>
    </section>

@stop

@section('content')
    <section class="content">
    {{-- row mini dash --}}
    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="info-box">
                
                    <span class="info-box-icon bg-red"><i class="fa fa-ticket"></i></span>
                
                <div class="info-box-content">
                
            
                    <span class="info-box-number">{{ Auth::guard('painelcliente')->User()->chamadoaberto()->count() }}</span>
                

                    <div class="text-sm">chamados em aberto</div>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
    </div>
    {{-- row mini dash --}}        
    {{-- row content --}}   

    </section>
@stop