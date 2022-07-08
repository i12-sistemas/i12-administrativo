@extends('layouts.admin.template')

@section('title', $title)

@section('menu-top')
@stop

@section('menu-top-right')                    
@stop


@section('content-header')
    <section class="content-header">
    <h1>Dispositivos Móveis</h1>
    <ol class="breadcrumb">
        <li> <a href="route('admin.home')" >home</a></li>
        <li> <a href="#" >Cadastros</a></li>
        <li class="active">Dispositivos</li>
    </ol>
    </section>

@stop

@section('content')
<section class="content">
<router-view></router-view>
</section>
@stop