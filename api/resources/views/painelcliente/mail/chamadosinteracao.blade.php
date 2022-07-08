@component('mail::message')

*******************************************************************************************
*** IMPORTANTE: NÃO RESPONDA ESSA MENSAGEM VIA E-MAIL, CLIQUE SOBRE O LINK DE INTERAÇÃO **
*******************************************************************************************

@if($ultimainteracao->origem=='C')
Prezado(a) <b>{{ $chamado->usuario->nome }}</b>,

Houve uma nova interação no chamado em sua fila.

---------------------------------------------
Número do chamado: <b>#{{ $chamado->id }}</b>

Data abertura: <b>{{ $chamado->dtabertura->format('d/m/Y H:i') }}</b>

Cliente: <b>{{ $chamado->cliente->nome }}</b>

@if($chamado->contato)
Contato: <b>{{ $chamado->contato->nome }}</b>
@endif
@endif

@if($ultimainteracao->origem=='O')
Prezado(a) <b>{{ $chamado->contato->nome }}</b> - {{ $chamado->cliente->nome }},

Houve uma nova interação no seu chamado, conforme consta abaixo:

Para interagir, clique no endereço abaixo:

{{$destinatario->link}}

@component('mail::button', ['url' => $destinatario->link])
Link para chamado #{{$chamado->id}}
@endcomponent
@endif

Status: <b>{{ $chamado->situacao == 'Encerrada' ? 'Encerrado' : 'Em aberto'  }}</b>

Assunto: <b>{{ $chamado->problemareclamado }}</b>

---------------------------------------------
<b>Última interação</b>

Data: <b>{{$ultimainteracao->datahora->format('d/m/Y H:i') }}</b>

{{$ultimainteracao->texto}}

---------------------------------------------

Atenciosamente,

<div><b>{{ $empresa->fantasia }}</b></div>
<div>{{ $empresa->telefone }}</div>
<div>{{ $empresa->email }}</div>
@endcomponent