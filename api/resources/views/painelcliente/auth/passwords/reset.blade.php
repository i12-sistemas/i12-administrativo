@extends('layouts.usuario.templatelogin')

@section('content')



<div class="login-box login-box-resetpw">
    <div class="login-logo">
        <a href="{{ route('site.home') }}">i<b>Stu</b></a>
        <h3>Área do usuário - Reset de senha</h3>
    </div>

    <div class="login-box-body">

        @if (!($tokenstatus==''))
        <div class="alert alert-danger">
            <p><strong>{{ $tokenstatus }}</strong></p>
        </div>        
        @else

        <div class="box-body box-profile">
            <div class="text-center"><img src="{{$associado->fotourlsmall}}" class="img img-responsive img-thumbnail center-block" /></div>
            <h3 class="text-center">Olá, <b>{{$associado->firstname}}</h3>
        </div>
        
        <p class="login-box-msg">Informe seu usuário após entre com a senha e confirme-a novamente</p>

        
            <div class="panel-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        {!! session('success') !!}
                    </div>
                    <div class="col-md-12">
                        <a href="{{route('usuario.login')}}" class="btn btn-default" >Voltar para tela de login</a>
                    </div>                                                     
                @endif

                @foreach ($errors->all() as $error)
                <div class="alert alert-danger">
                    <p><strong>{{ $error }}</strong></p>
                </div>
                @endforeach

                    <form class="form-horizontal" method="POST" action="{{route('usuario.resetpasswordconfirm')}}">
                        {{ csrf_field() }}

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="login" class="col-md-4 control-label">Login</label>

                            <div class="col-md-8">
                                <input id="login" type="text" minlength="1" class="form-control" name="login" value="{{ $login or old('login') }}" required autofocus>

                                @if ($errors->has('login'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('login') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Senha</label>

                            <div class="col-md-8">
                                <input id="password" type="password" minlength="3" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="col-md-4 control-label">Confirme a senha</label>
                            <div class="col-md-8">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary">Confirmar alteração</button>
                                <a href="{{route('usuario.login')}}"  class="btn btn-default">Ir p/ login</a>
                            </div>
                        </div>
                    </form>
            </div>
        @endif

    </div>
</div>

@endsection
