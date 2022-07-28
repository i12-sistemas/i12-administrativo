@component('mail::message')

# Olá,

Esta é uma mensagem de verificação de e-mail.

Digite o código abaixo no sistema solicitado

@if ($meiocomunicacao->sendlink === 1)
ou clique no link abaixo para validar este e-mail
@endif

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

@if (($meiocomunicacao->sendlink === 1) && ($url ? $url !== '' : false))
@component('mail::button', ['url' => $url])
Link de validação
@endcomponent

O link te levará para  [`{{$url}}`]({{$url}}).
@endif

<i>Não responda este e-mail.</i>

Obrigado,<br>
{{ config('app.name') }}
@endcomponent