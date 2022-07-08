@component('mail::message')

Olá <b>{{$prematricula->nome}}</b>

Você fez a pré-matricula no iStu - Gestão de Transporte - {{$empresa->fantasia}}, no entanto o e-mail informado ainda não foi validado.

Para efetivar a pré-matricula, acesse o link abaixo e aguarde carregar a tela de confirmação!

Acesse {{$link}}.

@component('mail::button', ['url' => $link])
Validar pré-matrícula
@endcomponent

Atenciosamente,

<div><b>{{ $empresa->fantasia }}</b></div>
@endcomponent