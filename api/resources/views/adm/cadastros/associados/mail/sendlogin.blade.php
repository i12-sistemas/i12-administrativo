@component('mail::message')

Olá <b>{{ $associado->firstname  }}</b>, 

Você está recebendo um informativo sobre seu login de acesso ao sistema de transporte - {{$empresa->fantasia}}.

<p>Nome: <b>{{ $associado->nome }}</b></p> 
<p>Matrícula: <b>{{ $associado->id }}</b></p>
<p>Login de acesso: <b>{{ $associado->login }}</b></p>

Acesse {{$linkacesso}}.
@component('mail::button', ['url' => $linkacesso])
Link para acesso ao sistema
@endcomponent


Esperamos que aprecie este informativo. 

Caso tenha dúvida responda este e-mail. Se preferir, entre em contato através do e-mail {{$empresa->email}} ou telefone {{$empresa->telefone}}


Obrigado,
<div><b>{{ $usuario->nome }}</b></div>
<div>{{ $usuario->email }}</div>
<div><b>{{ $empresa->fantasia }}</b></div>
<div>{{ $empresa->telefone }}</div>
<div>{{ $empresa->email }}</div>
<div><a href="{{ $site }}">{{ $site }}</a></div>
@endcomponent