@extends('layouts.admin.template')

@section('title', $title)

@section('menu-top')
@stop

@section('menu-top-right')                    
@stop

@section('content-header')
<section class="content-header">
    <h1>Vendas</h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.home')}}" >Home</a></li>
        <li><a href="{{route('admin.operacional.vendas')}}" >Vendas</a></li>
        <li> <a href="{{route('admin.operacional.vendas.consulta')}}" >Consultar</a></li>
        <li class="active">Nova venda</li>
    </ol>    
    </section>
@stop

@section('content')
<section class="content">
    <router-view></router-view>
</section>
@stop