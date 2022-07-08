@extends('layouts.admin.template')

@section('title', $title or '')

@section('content-header')
<section class="content-header">
    <h1>Fatura de venda</h1>
    <ol class="breadcrumb">
        <li><a href="{{route('admin.home')}}" > Home</a></li>
        {{-- <li><a href="{{route('admin.operacional.vendas')}}" > Vendas</a></li> --}}
        <li class="active">Visualizar</b></li>
    </ol>
    </section>
@stop


@section('content')
<section class="invoice">

    @if(session('success'))
        <h1>{{session('success')}}</h1>
    @endif


    @foreach ($errors->all() as $error)
    <div class="alert alert-danger">
        <p class="h4"><strong>{{ $error }}</strong></p>
    </div>
    @endforeach

    {{-- venda existe --}}
    @if(!$fatura)
        <div class="row">
            <div class="col-xs-12">
                <h2 class="page-header"><i class="fa fa-search"></i> Nenhuma fatura encontrada!</h2>
            </div>
        </div>    
    @else
    <!-- title row -->
    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header">
                <i class="fa fa-money"></i> Fatura de venda
                <small class="pull-right">Vencimento: {{$fatura->vencimento->format('d/m/Y')}}</small>
              
            </h2>
        </div>
    </div>

    <div class="row">
        <!-- accepted payments column -->
        <div class="col-xs-12">
            
            <p>Nº fatura: {{$fatura->id}}</p>
            <p>{!!$fatura->formapagto->categ . 
                            ($fatura->formapagto->pago==1 ? '' : ' - Vecto: <b>'. $fatura->vencimento->format('d/m/Y') . '</b>') .
                            ($fatura->ref=='' ? '' : '- Nº doc..: '. $fatura->ref . '</b>') 
                            !!}
                        @if($fatura->anexo<>'')
                            <span class="label pull-right"><a href="{{route('admin.operacional.financeiro.anexodownload', [$fatura->id, $fatura->anexo])}}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i> Download anexo</a></span>
                        @endif
            </p>
            <p>
                            <div class="text-right">
                                    @if($fatura->pago==1)
                                    <span class="text-primary pull-left"><i class="fa fa-check-circle" aria-hidden="true"></i> Quitado </span>
                                @else
                                    <span class="text-danger pull-left"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Em aberto </span>
                                @endif

                                <strong>{{formatRS($fatura->valor)}}</strong>
                            </div>
                        </P>

        </div>
    </div>

    <div class="row no-print">
        <div class="col-xs-12 text-center">
            <div class="btn-group ">
                <a href="{{route('admin.operacional.vendas.show', [$fatura->idvenda])}}" class="btn btn-default"><i class="fa fa-level-up" aria-hidden="true"></i> Venda nº {{$fatura->idvenda}} </a>
                @if( $fatura->anexo)
                <a href="{{route('admin.operacional.financeiro.anexodownload', [$fatura->id, $fatura->anexo])}}" target="_blank" class="btn btn-default"><i class="fa fa-paperclip" aria-hidden="true"></i> Download anexo</a>
                @endif
            </div>
              
        </div>
    </div>    
    <hr>
    {{-- dados do associado --}}
    <div class="row invoice-info">
        <div class="col-sm-2 invoice-col">
            <img src="{{$fatura->associado->fotourl}}" class="img img-responsive img-thumbnail" style="max-height: 140px; max-width: 140px" />
        </div>
        <div class="col-sm-6 invoice-col">
            Associado
            <address>
            <strong>{{$fatura->associado->nome}}</strong><br>
            <strong>Matrícula: {{$fatura->associado->id}}</strong><br>
            {{$fatura->associado->telefone=='' ? '' : 'Tel: ' . $fatura->associado->telefone . '<br>'}}
            {{$fatura->associado->email=='' ? '' : 'E-mail: ' . $fatura->associado->email}}
            </address>
        </div>
    </div>
    {{-- dados do associado --}}
          
      

</section>

@if($fatura->anexo<>'')
<section class="invoice">
    <form action="{{ route('admin.operacional.financeiro.removeranexo', [$fatura->id, $fatura->anexo]) }}" method="POST">
        {!! csrf_field() !!}
        <div class="row">
            <div class="col-xs-12">
            <h2 class="page-header"><i class="fa fa-paperclip" ></i> Anexo da fatura</h2>
            </div>
        </div>
        <div class="row invoice-info">
            <div class="col-xs-12">
                <div class="checkbox">
                <label>
                    <input type="checkbox" required> Estou ciente que após excluir o anexo não é possível reverter a operação!
                </label>
                </div>
            </div>
        </div>      
        <div class="row no-print">
            <div class="col-xs-12">
                <button type="submit" class="btn btn-danger"><i class="fa fa-ban"></i> Excluir anexo?</button>
                <a href="{{route('admin.operacional.financeiro.anexodownload', [$fatura->id, $fatura->anexo])}}" target="_blank" class="btn btn-default"><i class="fa fa-paperclip" aria-hidden="true"></i> Download anexo</a>
            </div>
        </div>       
    </form>
</section>  
@else
<section class="invoice">
        <form action="{{ route('admin.operacional.financeiro.addanexo', [$fatura->id]) }}" method="POST" enctype="multipart/form-data">
            {!! csrf_field() !!}
            <div class="row">
                <div class="col-xs-12">
                <h2 class="page-header"><i class="fa fa-paperclip" ></i> Anexo da fatura</h2>
                </div>
            </div>
            <div class="row invoice-info">
                <div class="col-xs-12">
                    <div class="checkbox">
                    <label>
                        <input type="file" required name="anexo" id="anexo"  accept="application/pdf,image/jpeg" />
                    </label>
                    </div>
                </div>
            </div>      
            <hr>
            <div class="row no-print">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-ban"></i> Salvar anexo</button>
                </div>
            </div>       
        </form>
    </section>  
@endif
     
@endif     
@stop
