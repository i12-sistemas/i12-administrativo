@component('mail::message')

Registro de contato através do site de sistema de transporte {{$empresa->fantasia}}.

<h3>Dados da mensagem</h3>

<p>Nome: <b>{{ $nome }}</b></p> 
<p>E-mail: <b>{{ $email }}</b></p> 
<p>Telefone: <b>{{ $telefone }}</b></p> 
<p>Assunto: <b>{{ $assunto }}</b></p>
<p>Mensagem</p>
<p><b>{{ $mensagem }}</b></p>


Atenciosamente,

<div><b>{{ $empresa->fantasia }}</b></div>
@endcomponent