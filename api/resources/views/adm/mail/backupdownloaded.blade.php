@component('mail::message')
# Registro de download de backup<br>
@component('mail::panel')
Arquivo: **{{ $arquivo->shortfilename }}**<br>
Tamanho: **{{ human_filesize( $arquivo->size ) }}**<br>
Última modificação: *{{ $arquivo->lastmodified->format('d/m/Y H:i') }}*<br>
<br>
@if($arquivo->cliente)
Cliente: *{{ $arquivo->cliente->nome }}*<br>
Cidade: *{{ $arquivo->cliente->cidade }}*<br>
@endif
<br>
Usuário: **{{ $usuario->login }}**<br>
Data: **{{ $info->dhacao->format('d/m/Y H:i:s') }}**<br>
IP: **{{ $info->ip }}**<br>
@endcomponent

<br>
Atenciosamente,<br>

**{{ $usuario->nome }}**<br>
*{{ config('app.name') }}*<br>
@endcomponent
