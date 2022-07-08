@component('mail::message')

Olá, {{ $associado->nome  }}, 

Você está recebendo um link informativo para acompanhar o projeto <b>{{ $associado->nome }}</b> - {{ $associado->nome }}.

{{--  @component('mail::button', ['url' => $share->linkpublico])
Link do Status Report
@endcomponent  --}}

<a class="link btn-link" href="{{ $associado->nome }}" target="_blank" >{{$associado->nome}}</a>

Esperamos que aprecie este informativo, caso queria cancelar este tipo recebimento e-mail entre em contato com suporte técnico.


Obrigado,
<div><b>{{ $associado->nome }}</b></div>
<div>{{ $associado->nome }}</div>
@endcomponent