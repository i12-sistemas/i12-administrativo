@component('mail::message')

# Olá,

Esta é uma mensagem de verificação de e-mail.

Clique no link abaixo para validar ou digite o código abaixo no sistema solicitado.

@if ($meiocomunicacao->message ? $meiocomunicacao->message<>'' : false)
@component('mail::panel')
{{$meiocomunicacao->message}}
@endcomponent
@endif

@component('mail::panel')
# Seu código de validação
# {{$meiocomunicacao->code}}
@endcomponent
<i>Este código é válido até {{$meiocomunicacao->expire_at->format('d/m/Y - H:i:s')}}</i><br>

@component('mail::button', ['url' => 'http://www.idoze.com.br'])
Link de validação
@endcomponent

<i>Não responda este e-mail.</i>

Obrigado,<br>
{{ config('app.name') }}
@endcomponent