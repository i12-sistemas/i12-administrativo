@component('mail::message')

Olá <b>{{ $associado->firstname  }}</b>, 

Foi solicitado o reset de senha para acesso ao sistema de transporte - {{$empresa->fantasia}}.

<p>Nome: <b>{{ $associado->nome }}</b></p> 
<p>Matrícula: <b>{{ $associado->id }}</b></p>
<p>Login de acesso: <b>{{ $associado->login }}</b></p>

Acesse este link {{$link}} e cadastre sua nova senha.
@component('mail::button', ['url' => $link])
Link para cadastro de nova senha
@endcomponent


Caso não tenha solicitado esta alteração entre em contato através do e-mail {{$empresa->email}} ou telefone {{$empresa->telefone}}

Obrigado,
<div><b>{{ $empresa->fantasia }}</b></div>
<div>{{ $empresa->telefone }}</div>
<div>{{ $empresa->email }}</div>
<div><a href="{{ $site }}">{{ $site }}</a></div>
@endcomponent