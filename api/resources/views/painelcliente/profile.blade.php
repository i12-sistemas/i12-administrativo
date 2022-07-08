@extends('layouts.usuario.template')

@section('title', $title)

@section('menu-top')
@stop

@section('menu-top-right')
@stop


@section('content-header')
    <section class="content-header">
    <h1>Perfil</h1>
    <ol class="breadcrumb">
        <li><a href="{{route('usuario.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Meus dados</li>
    </ol>
    </section>

@stop

@section('content')
      <section class="content">

<div class="row">
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="-responsive img-thumbnail" src="{{ Auth::guard('painelcliente')->User()->fotourl }}" alt="Foto do usuário">

              <h3 class="profile-username text-center">{{Auth::guard('painelcliente')->User()->firstname}}</h3>

              <p class="text-muted text-center">{{Auth::guard('painelcliente')->User()->nome}}</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Nº matrícula</b> <a class="pull-right">{{Auth::guard('painelcliente')->User()->id}}</a>
                </li>
                <li class="list-group-item">
                  <b>Login</b> <a class="pull-right">{{Auth::guard('painelcliente')->User()->login}}</a>
                </li>
                <li class="list-group-item">
                  <b>CPF</b> <a class="pull-right">{{Auth::guard('painelcliente')->User()->cpf}}</a>
                </li>
              </ul>

              <hr>
              <div class="text-right">
              <form  method="POST" action="{{route('usuario.resetpassword')}}">
                  {{ csrf_field() }}
                  <input id="login" type="hidden" class="form-control" name="login" value="{{ Auth::guard('painelcliente')->User()->login }}">
                  <button type="submit" class="btn btn-primary">Alterar senha</button>
              </form>
              @if (session('success'))
                <div class="text-primary">
                    {!! session('success') !!}
                </div>
              @endif              
            </div>


            </div>
          </div>
        </div>

        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#dados" data-toggle="tab" aria-expanded="false">Meus dados</a></li>
              {{-- <li class=""><a href="#rotas" data-toggle="tab" aria-expanded="true">Rotas</a></li>
              <li class=""><a href="#anexos" data-toggle="tab" aria-expanded="true">Anexos</a></li> --}}
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="dados"> 
                <div class="box-header"><strong><i class="fa fa-book" aria-hidden="true"></i> Dados do pessoais</strong></div>
                <div class="box-body">
                <dl class="dl-horizontal">
                    <dt>CPF</dt>
                    <dd>{{Auth::guard('painelcliente')->User()->cpf}}</dd>
                    <dt>RG</dt>
                    <dd>{{Auth::guard('painelcliente')->User()->rg}}</dd>
                    <dt>Data de nascimento</dt>
                    <dd>{{Auth::guard('painelcliente')->User()->dtnasc->format('d/m/Y')}}</dd>
                    <dt>Telefones</dt>
                    <dd>{{Auth::guard('painelcliente')->User()->tel1 . ' ' . Auth::guard('painelcliente')->User()->tel2}}</dd>
                    <dt>E-mail *</dt>
                    <dd>{{Auth::guard('painelcliente')->User()->email}}</dd>
                </dl>
                </div>
                <hr>
                <div class="box-header"><strong><i class="fa fa-book" aria-hidden="true"></i> Dados do curso</strong></div>
                <div class="box-body">
                <dl class="dl-horizontal">
                    <dt>Curso</dt>
                    <dd>{{Auth::guard('painelcliente')->User()->curso}}</dd>
                    <dt>Instituição</dt>
                    <dd>{{Auth::guard('painelcliente')->User()->instituicaoensino}}</dd>
                    <dt>Ano</dt>
                    <dd>{{Auth::guard('painelcliente')->User()->cursoano}}</dd>
                    <dt>Semestre</dt>
                    <dd>{{Auth::guard('painelcliente')->User()->cursosemestre}}</dd>
                    <dt>Término</dt>
                    <dd>{{Auth::guard('painelcliente')->User()->cursotermino->format('d/m/Y')}}</dd>
                </dl>
                </div>
                <hr>
                <div class="box-header"><strong><i class="fa fa-book" aria-hidden="true"></i> Endereço</strong></div>
                <div class="box-body">
                <dl class="dl-horizontal">
                    <dt>Endereço</dt>
                    <dd>{{Auth::guard('painelcliente')->User()->logradouro . ' ' . Auth::guard('painelcliente')->User()->endereco . ', ' . Auth::guard('painelcliente')->User()->numero}}</dd>
                    <dt>Complemento</dt>
                    <dd>{{Auth::guard('painelcliente')->User()->compl}}</dd>
                    <dt>Bairro</dt>
                    <dd>{{Auth::guard('painelcliente')->User()->bairro}}</dd>
                    <dt>CEP</dt>
                    <dd>{{Auth::guard('painelcliente')->User()->cep}}</dd>
                    <dt>Cidade</dt>
                    <dd>{{Auth::guard('painelcliente')->User()->cidade . ' / ' . Auth::guard('painelcliente')->User()->uf}}</dd>
                </dl>
                <div class="pull-right"><i>Para solicitar a alteração de dados registre um protocolo.</i></div>
                </div>
                

              </div>

              {{-- <div class="tab-pane " id="anexos">
                <h1>Meus anexos aqui</h1>
              </div>
       

              <div class="tab-pane" id="rotas">
                <h1>Minhas rotas aqui</h1>
              </div> --}}

            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>


      </section>
@stop