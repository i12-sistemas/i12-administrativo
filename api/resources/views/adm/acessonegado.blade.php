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
    </ol>
    </section>
@stop





@section('content')
<section class="content">
    <div class="error-page">
    <h2 class="headline text-red"><i class="fa fa-user-times" aria-hidden="true"></i></h2>

    <div class="error-content">
        <h3><i class="fa fa-warning text-red"></i> Oops! Sem permissão.</h3>

        <p>Você esta sem permissão de acesso a esta função.</p>
        <p>Procure o administrador do sistema e solicite permissão através da identificação:</p>
        @if($permissao)
            <h3>{{$permissao->descricao}}</h3>
            <h4>ID: <b>{{ $permissao->id}}</b></h4>
            
        @else
            <h4><small>ID: </small>{{$idpermissao or 'Permissão não identificada!'}}</h4>
        @endif

        

        {{-- <form class="search-form">
        <div class="input-group">
            <input type="text" name="txtjustificativa" class="form-control" placeholder="Justifique aqui o motivo">

            <div class="input-group-btn">
            <button type="submit" name="submit" class="btn btn-danger btn-flat"><i class="fa fa-send"></i>
            </button>
            </div>
        </div>
        </form> --}}
        <div><a href="{{URL::previous(-1)}}" class="btn btn-link text-red"><i class="fa fa-arrow-left" aria-hidden="true"></i> Voltar</a></div>
    </div>
    <!-- /.error-content -->
    </div>
</section>
@stop

@section('css')
    <link rel="stylesheet" href="/css/icom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop
