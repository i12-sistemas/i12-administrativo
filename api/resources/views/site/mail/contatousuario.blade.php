@component('mail::message')

Olá <b>{$nome}}</b>

Agradeçemos seu contato através do site do sistema de transporte {{$empresa->fantasia}}.

Encaminhamos seu contato para nossos administratores que em breve responderão!

<h3>Dados da mensagem</h3>
<p>Nome: <b>{{ $nome }}</b></p> 
<p>Telefone: <b>{{ $telefone }}</b></p> 
<p>Assunto: <b>{{ $assunto }}</b></p>
<p>Mensagem</p>
<p><b>{{ $mensagem }}</b></p>

Atenciosamente,

<div><b>{{ $empresa->fantasia }}</b></div>
@endcomponent