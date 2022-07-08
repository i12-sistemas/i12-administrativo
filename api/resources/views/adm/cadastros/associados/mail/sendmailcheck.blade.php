@component('mail::message')

Olá <b>{{ $associado->firstname  }}</b>, 

Foi solicitado a confirmação do seu e-mail no cadastro do sistema de transporte - {{$empresa->fantasia}}.

<p>E-mail: <b>{{ $associado->email }}</b></p> 
<p>Nome: <b>{{ $associado->nome }}</b></p> 
<p>Matrícula: <b>{{ $associado->id }}</b></p>

Acesse este link {{$link}} e aguarde a mensagem de confirmação de e-mail!

@component('mail::button', ['url' => $link])
Confirmar meu e-mail
@endcomponent

Caso não tenha solicitado esta alteração entre em contato através do e-mail {{$empresa->email}} ou telefone {{$empresa->telefone}}

Obrigado,
<div><b>{{ $empresa->fantasia }}</b></div>
<div>{{ $empresa->telefone }}</div>
<div>{{ $empresa->email }}</div>
<div><a href="{{ $site }}">{{ $site }}</a></div>
@endcomponent