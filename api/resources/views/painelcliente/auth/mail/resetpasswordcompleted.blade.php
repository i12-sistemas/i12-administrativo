@component('mail::message')

Olá <b>{{ $associado->firstname  }}</b>, 

Sua senha foi alterada com sucesso!

<p>Login de acesso: <b>{{ $associado->login }}</b></p>

@component('mail::button', ['url' => $link])
Acessar sistema
@endcomponent

Caso não tenha solicitado esta alteração entre em contato através do e-mail {{$empresa->email}} ou telefone {{$empresa->telefone}}

Obrigado,
<div><b>{{ $empresa->fantasia }}</b></div>
<div>{{ $empresa->telefone }}</div>
<div>{{ $empresa->email }}</div>
<div><a href="{{ $site }}">{{ $site }}</a></div>
@endcomponent