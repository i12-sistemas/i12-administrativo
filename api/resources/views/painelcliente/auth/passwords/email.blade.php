@extends('layouts.usuario.templatelogin')
@section('content')

    <div class="login-box">
        <div class="login-logo">
            <a href="{{ route('site.home') }}">i<b>Stu</b></a>
            <h3>Área do usuário - Reset de senha</h3>
        </div>

        <div class="login-box-body">
            <p class="login-box-msg">Entre com seu usuário e senha para iniciar</p>
   
            
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

                    @if (!session('success'))
                    <form class="form-horizontal" method="POST" action="{{route('usuario.resetpassword')}}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('login') ? ' has-error' : '' }}">
                            <label for="login" class="col-md-3 control-label">Usuário</label>

                            <div class="col-md-9">
                                <input id="login" type="text" class="form-control" name="login" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('login'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('login') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group text-rigth">
                            <div class="col-md-9 col-md-offset-3 text-rigth">
                                <button type="submit" class="btn btn-primary">
                                    Enviar link de reset de senha
                                </button>
                            </div>
                            <div class="col-md-9 col-md-offset-3">
                                <a href="{{route('usuario.login')}}" class="btn btn-link" >Voltar</a>
                            </div>                            
                        </div>
                    </form>
                     @endif
                </div>


        </div>
    </div>




    
@endsection
