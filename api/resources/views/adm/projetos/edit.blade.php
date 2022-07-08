{{-- resources/views/admin/dashboard.blade.php --}}
@extends('adminlte::page')

@section('jsbeforebody')
@stop

@section('title', ' - ' . $title)

@section('content_header')

<section class="content-header">
    <h1 class="hidden-xs-down" >Projeto</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('projetos.consultor') }}">Meus projetos</a></li>
        <li class="active">Projeto</li>
    </ol>
  </section>


@stop

@section('content')


<section class="content">
@if (\Session::has('success'))
    <div class="form-group">
    <div class="row col-md-12">
    <div class="alert alert-success">
        {!! \Session::get('success') !!}
    </div>
    </div>
    </div>
@endif 

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif         

        
@if (count($projeto)==0)
    <div class="alert alert-success">
        <p>Nenhum projeto encontrado</p>
    </div>
@else  
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class=""><a href="#dados" data-toggle="tab" aria-expanded="false"><i class="fa fa-edit"></i></a></li>
        <li class="">
            <a href="#atividades" data-toggle="tab" aria-expanded="false">Atividades
                <span class="pull-right-container label pull-right text-black bg-info">{{ $projeto->atividades->count() }}</span>
            </a>
        </li>
        <li class=""><a href="#consultores" data-toggle="tab" aria-expanded="true"><i class="fa fa-users"></i></a></li>
        <li class="active"><a href="#sharing" data-toggle="tab" aria-expanded="true"><i class="fa fa-globe"></i></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" id="dados">

                <div class="row">
                    <div class="col-xs-12">
                        <h3>{{ $projeto->numproposta }}</h3>
                        <h5>{{ $projeto->nomeprojeto }}</h5>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-xs-12 col-md-8">
                        <div class="col-xs-12">
                            <span class="col-xs-12 col-md-3">Cliente:</span>
                            <div class="col-xs-12 col-md-9"><span class="text-bold">{{ $projeto->idcliente }}</span></div>    
                        </div>
                        <div class="col-xs-12">
                            <span class="col-xs-12 col-md-3">Resp. Comercial:</span>
                            <div class="col-xs-12 col-md-9"><span class="text-bold">{{ $projeto->idusuarioresp }}</span></div>    
                        </div>
                        <div class="col-xs-12">
                            <span class="col-xs-12 col-md-3">Resp. Técnica:</span>
                            <div class="col-xs-12 col-md-9"><span class="text-bold">{{ $projeto->idusuariorespterc }}</span></div>    
                        </div>                   
                    </div>
                    <div class="col-xs-12 col-md-4">
                        <div class="col-xs-12">
                            <span class="col-xs-12 col-md-3">Emissão:</span>
                            <div class="col-xs-12 col-md-9"><span class="text-bold">{{ $projeto->dtemissao->format('d/m/Y') }}</span></div>    
                        </div>
                        <div class="col-xs-12">
                            <span class="col-xs-12 col-md-3">Válido:</span>
                            <div class="col-xs-12 col-md-9"><span class="text-bold">{{ $projeto->dtvalidade->format('d/m/Y') }}</span></div>    
                        </div>
                        <div class="col-xs-12">
                            <span class="col-xs-12 col-md-3">Empresa:</span>
                            <div class="col-xs-12 col-md-9"><span class="text-bold">{{ $projeto->idempresaresp }}</span></div>    
                        </div>
                        <div class="col-xs-12">
                            <span class="col-xs-12 col-md-3">Categoria</span>
                            <div class="col-xs-12 col-md-9"><span class="text-bold">{{ $projeto->categ }}</span></div>    
                        </div>
                    </div>
                </div>

                 <div class="row">
                    <div class="col-xs-11 col-xs-offset-1">
                        <a href='{{ URL::previous() }}' class='btn btn-default'><i class="glyphicon glyphicon-backward"></i> Voltar</a>
                    </div>
                </div>

            

        </div>
        <!-- /.tab-pane -->
        <div class="tab-pane" id="atividades">
            <router-view numproposta='{!! $projeto->numproposta !!}' name="projetoAtividades" ></router-view>
        </div>
        <div class="tab-pane" id="consultores">
            <div class="table-responsive">
            <table class="table table-hover table-striped table-condensed">
                @if($consultores)
                @foreach ($consultores as $consultor)
                    <tr>
                    <td>
                        {!! ($consultor->id==Auth::user()->id) ? '<b>** Eu ** </b>' : $consultor->nome !!}
                    </td>
                    </tr>
                @endforeach
                @else
                    <tr><td>Nenhum consultor liberado</td></tr>                
                @endif
            </table>
            </div>

        </div>        
        <div class="tab-pane active" id="sharing">
            <router-view numproposta='{!! $projeto->numproposta !!}' name="projetoSharing" ></router-view>
        </div>         
        <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
</div>


@endif
</section>
@stop

@section('css')
    <link rel="stylesheet" href="/css/sapv.css">
@stop


@section('js')
    <script>
        var userlogged = {
                        id: {!! Auth::user()->id !!}, 
                        nome: "{!! Auth::user()->nome !!}",
                        permissoes: [
                            @foreach (Auth::user()->permissoes() as $permissao)
                                "{!! $permissao->idpermissao !!}",    
                            @endforeach
                            ],
                        };
        var base_url = "{!! url('/') !!}";    
    </script>
    @if(config('app.env')=='production')
    <script src="{{ asset('vendor/vuejs/vue.min.js') }}"></script>
    @else
    <script src="{{ asset('vendor/vuejs/vue.js') }}"></script>
    @endif
    <script src="{{ asset('js/app.js') }}"></script>
@stop