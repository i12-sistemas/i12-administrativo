@component('mail::message')

# Código de redefinição de senha

Olá, {{$token->contato->nome}}

Use esse código para redefinir a senha da conta no {{env('APP_NAME')}}.

@component('mail::panel')
### Aqui está o seu código:
# {{$token->codenumber}}
@endcomponent

@component('mail::button', ['url' => $url, 'color' => 'blue'])
Alterar senha agora
@endcomponent

O link te levará para  [`{{$url}}`]({{$url}}).

<i>A solicitação foi feita através do IP {{$token->ip}}</i>

Obrigado,

Equipe de contas da {{env('APP_NAME')}}
@endcomponent
