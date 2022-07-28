@component('mail::message')
Olá, <b>{{ $atendimento->usuario->nome }}</b>,

Você tem uma nova mensagem do cliente.

@component('mail::panel')
Número do atendimento: <b>#{{ $atendimento->id }}</b><br>
Código de acesso: <b>#{{ $atendimento->codigoacesso }}</b><br>
Data abertura: <b>{{ $atendimento->dtabertura->format('d/m/Y H:i') }}</b><br>
Cliente: <b>{{ $atendimento->cliente->nome }}</b><br>
@if($atendimento->contato)
Contato: <b>{{ $atendimento->contato->nome }}</b><br>
@endif
@endcomponent


@component('mail::panel')
Data: {{$interacao->datacad->format('d/m/Y H:i')}}<br>

{{$interacao->texto}}
@endcomponent


*******************************************************************
*ATENÇÃO: Esta é uma mensagem automática. Favor não respondê-la.


Atenciosamente,

{{ config('app.name') }}
@endcomponent