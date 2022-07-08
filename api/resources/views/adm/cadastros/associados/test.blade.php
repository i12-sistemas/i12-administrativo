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
<h1>clientes</h1>
<router-view></router-view>
</section>
@stop