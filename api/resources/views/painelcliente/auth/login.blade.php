@extends('painelcliente.layout.templatelogin')

@section('content')

    <div class="login-box">
        <div class="login-logo">
            <a href="{{ route('painelcliente.home') }}">i<b>12</b></a>
            <h3>Painel do cliente</h3>
        </div>

        <div class="login-box-body">
            <p class="login-box-msg">Entre com seu usuário e senha para iniciar</p>
            <form action="{{ route('painelcliente.login') }}" method="post">
                {!! csrf_field() !!}

                @foreach ($errors->all() as $error)
                <div class="form-group has-feedback has-error">
                <span class="text-danger">
                    <strong>{{ $error }}</strong>
                </span>
                </div>
                @endforeach


                @if (session('success'))
                    <div class="alert alert-success">
                        {!! session('success') !!}
                    </div>                                                   
                @endif  

                
                <div class="form-group has-feedback {{ $errors->has('email') ? 'has-error' : '' }}">
                    <input type="text" name="login" class="form-control" value="{{ old('login') }}" placeholder="Usuário">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    @if ($errors->has('login'))
                        <span class="help-block">
                            <strong>{{ $errors->first('login') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group has-feedback {{ $errors->has('cnpj') ? 'has-error' : '' }}">
                    <input type="text" name="cnpj" class="form-control" value="{{ old('cnpj') }}" placeholder="CNPJ">
                    <span class="glyphicon glyphicon-certificate form-control-feedback"></span>
                    @if ($errors->has('cnpj'))
                        <span class="help-block">
                            <strong>{{ $errors->first('cnpj') }}</strong>
                        </span>
                    @endif
                </div>                
                <div class="form-group has-feedback {{ $errors->has('password') ? 'has-error' : '' }}">
                    <input type="password" name="password" class="form-control"
                           placeholder="Senha">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="remember">Lembrar-me
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <button type="submit"
                                class="btn btn-primary btn-block btn-flat">Acessar</button>
                    </div>
                </div>
            </form>
            <div class="auth-links">
                <a href="{{ route('painelcliente.esqueciminhasenha')}}" class="text-center" >Esqueci minha senha</a>
                <br>
            </div>
        </div>
    </div>



@stop