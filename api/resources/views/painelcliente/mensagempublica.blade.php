@extends('layouts.usuario.templatelogin')
@section('content')
<div class="login-box login-box-resetpw">
    <div class="login-logo">
        <a href="{{ route('site.home') }}">i<b>Stu</b></a>
        <h3>{{$title or 'Sistema de Gestão de transporte'}}</h3>
    </div>

    <div class="login-box-body">

        @if(isset($associado))
        <h2>Olá, {{$associado->firstname}}</h2>
        @endif        

            @foreach ($errors->all() as $error)
            <div class="alert alert-danger">
                <strong>{{ $error }}</strong>
            </div>
            @endforeach


            @if (isset($msg))
                <div class="alert alert-success">
                    {!! $msg !!}
                </div>                 
                                                  
            @endif  


    </div>
</div>
@stop