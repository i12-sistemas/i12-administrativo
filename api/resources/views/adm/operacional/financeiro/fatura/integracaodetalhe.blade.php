@extends('layouts.admin.template')

@section('title', $title or '')

@section('content-header')
<section class="content-header">
    <h1>Conciliação - Baixa de recebimento</h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.home')}}" > Home</a></li>
    </ol>
    </section>
@stop


@section('content')
<section class="content">
<router-view :idarquivo="{{$id}}"></router-view>
</section>
@stop
