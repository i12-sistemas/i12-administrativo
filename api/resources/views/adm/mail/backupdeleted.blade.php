@component('mail::message')
@component('mail::panel')
# Arquivos de backup excluídos
@endcomponent

Usuário: **{{ $usuario->login }}**<br>
Data: **{{ $info->dhacao->format('d/m/Y H:i:s') }}**<br>
IP: **{{ $info->ip }}**<br>
<br>
Espaço liberado: **{{ human_filesize(  $info->size ) }}** - *{{ $info->qtde }} arquivo(s)*<br>


@component('mail::table')
| Arquivo | Última modificação | Tamanho |
|---	|---	|--:	|
@foreach ($arquivos as $arquivo)
  | {{ $arquivo->filename }} | {{ $arquivo->lastmodified->format('d/m/Y H:i') }} | {{ human_filesize( $arquivo->size ) }}
@endforeach
@endcomponent

Atenciosamente,

**{{ $usuario->nome }}**<br>
*{{ config('app.name') }}*<br>
@endcomponent
