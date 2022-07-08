@component('mail::message')

Olá <b>{{ $destinatario->nome  }}</b>, 

@if($tipo=='interacao')
Uma nova mensagem foi adicionada ao protocolo {{$protocolo->protocolo}} - {{$protocolo->assunto}}
@elseif($tipo=='novoprotocolo')
Um novo protocolo foi registrado - {{$protocolo->protocolo}} - {{$protocolo->assunto}}
@endif

----------

<p>Mensagem</p> 
<p><b>{{ $msg->msg }}</b></p>
@if($msg->privado==1)
<p style="color:red">Mensagem privada - uso somente para administradores</p>
@endif

----------

Acesse o seu painel de controle pelo link {{$link}} para ler os detalhes e interagir se necessário.

@component('mail::button', ['url' => $link])
Acessar protocolo
@endcomponent


*E-mail somente informativo - não responder, se necessário acesse o painel de controle para interagir!*

Caso não tenha solicitado esta alteração entre em contato através do e-mail {{$empresa->email}} ou telefone {{$empresa->telefone}}

Obrigado,
<div><b>{{ $empresa->fantasia }}</b></div>
<div>{{ $empresa->telefone }}</div>
<div>{{ $empresa->email }}</div>
<div><a href="{{ $site }}">{{ $site }}</a></div>
@endcomponent