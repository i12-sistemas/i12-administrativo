@component('mail::message')
@component('mail::panel')
## Nível pago<br>
{{$pago}} clientes(s) - {{$indicepago}}%<br>
@endcomponent
@component('mail::panel')
## Nível gratuito<br>
{{$gratuito}} clientes(s) - {{$indicegratuito}}%<br>
@endcomponent
@component('mail::button', ['url' => route('adm.backup.report.alert'), 'color' => 'red'])
{{$indicepago + $indicegratuito}}% dos backups em alerta
@endcomponent

<br><br><br>

# Lista de backup em alerta ou risco

______
<br>

@foreach ($dados as $item)
## <a href="{{route('adm.backup.list.files', ['diretorio' => $item->doc14char ])}}" class="text-black">{{ $item->nome }}</a>
{{ (($item->fantasia != $item->nome) ? $item->fantasia . ' | ' : '') . $item->cidade}}<br>
>> Nível de backup: *{{ $item->i12controlabkp == 1 ? 'Gratuito' : '** PAGO **' }}*<br>
@if($item->lastmodified)
>> Último backup {{\Carbon\Carbon::parse($item->lastmodified)->diffForHumans()}}<br>
>> {{ \Carbon\Carbon::parse($item->lastmodified)->format('d/m/Y H:i')}}<br>
>> Consumo em disco: {{ human_filesize( $item->totalsize ) }}<br>
@else
>> Nenhum arquivo de backup<br>
@endif
______
@endforeach

*{{ config('app.name') }}*<br>
@endcomponent