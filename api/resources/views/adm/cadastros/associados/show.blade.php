@extends('layouts.admin.template')

@section('title', $title)

@section('menu-top')
@stop

@section('menu-top-right')
@stop


@section('content-header')
    <section class="content-header">
    <h1>Associado</h1>
    <ol class="breadcrumb">
        <li> <a href="{{ route('admin.home') }}" >home</a></li>
        <li> <a href="{{ route('admin.home') }}" >Cadastros</a></li>
        <li> <a href="{{ route('admin.cadastros.associados') }}">Associados</a></li>
    </ol>
    </section>

@stop

@section('content')
      <section class="content">
        <router-view :idassociado="{{ $id == 'novo' ? '-1':  $id }}" modo="{{ $id == 'novo' ? 'add' : 'update' }}"></router-view>
      </section>
@stop