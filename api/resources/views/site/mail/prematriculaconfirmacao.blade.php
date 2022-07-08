@component('mail::message')

Olá <b>{{$prematricula->nome}}</b>

Esta confirmado a pré-matricula no iStu - Gestão de Transporte - {{$empresa->fantasia}}.

Caso queira imprimir seu comprovante de inscrição clique no link abaixo.

{{$link}}.

@component('mail::button', ['url' => $link])
Imprimir comprovante
@endcomponent

Atenciosamente,

<div><b>{{ $empresa->fantasia }}</b></div>
@endcomponent