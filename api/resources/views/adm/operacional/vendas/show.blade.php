@extends('layouts.admin.template')

@section('title', $title or '')

@section('content-header')
<section class="content-header">
    <h1>Vendas</h1>
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
    @if(!$venda && $vendacancelada)
<!-- title row -->
    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header">
                <i class="fa fa-ban"></i> Venda Cancelada - Nº {{$vendacancelada->id}}
                <small class="pull-right">Data emissão da venda: {{$vendacancelada->dhregistro->format('d/m/Y')}}</small>
                @if($vendacancelada->idpai>0)
                <small class="pull-right margin-r-5">
                    <a href="{{route('admin.operacional.vendas.show', [$vendacancelada->idpai])}}">Venda mestre: {{$vendacancelada->idpai}}</a> | 
                </small>
                @endif                
            </h2>
        </div>
    </div>
    <!-- info row -->
    <div class="row invoice-info">
      <!-- /.col -->
      <div class="col-sm-2 invoice-col">
        <img src="{{$vendacancelada->associado->fotourl}}" class="img img-responsive img-thumbnail" style="max-height: 140px; max-width: 140px" />
      </div>
      <div class="col-sm-6 invoice-col">
        Associado
        <address>
          <strong>{{$vendacancelada->associado->nome}}</strong><br>
          <strong>Matrícula: {{$vendacancelada->associado->id}}</strong><br>
          {{$vendacancelada->associado->enderecocompleto}}<br>
          {{$vendacancelada->associado->bairro}}<br>
          CEP: {{$vendacancelada->associado->cep}}<br>
          {{$vendacancelada->associado->telefone=='' ? '' : 'Tel: ' . $vendacancelada->associado->telefone . '<br>'}}
          {{$vendacancelada->associado->email=='' ? '' : 'E-mail: ' . $vendacancelada->associado->email}}
        </address>
      </div>
      <div class="col-sm-4 invoice-col">
            Emitido por
            <address>
                @if($vendacancelada->origem==0)
                    Usuário: <strong>{{$vendacancelada->usuario->nome}}</strong><br>
                @else
                    <strong>Pelo próprio associado</strong>
                @endif
                IP: {{$vendacancelada->ipregistro}}<br>
            </address>
        </div>      

        <div class="col-sm-12">
            <div class="alert h4">
            <p>Motivo: <strong>{{$vendacancelada->motivoexclusao}}</strong></p>
                <p>Por quem: <strong>{{$vendacancelada->usuarioexclusao->nome}}</strong></p>
                <p>Quando: {{$vendacancelada->dhexclusao->format('d/m/Y h:i')}}</p>
            </div>
        </div>
            
    </div>
    <!-- this row will not appear when printing -->
    <div class="row no-print">
      <div class="col-xs-12 text-center">
          <div class="btn-group ">
                @if($vendacancelada->idpai>0)
                <a href="{{route('admin.operacional.vendas.show', [$vendacancelada->idpai])}}" class="btn btn-default"><i class="fa fa-level-up" aria-hidden="true"></i> Venda mestre: {{$vendacancelada->idpai}} </a>

                @endif 
          </div>
        
      </div>
    </div>

        
    
    @elseif(!$venda && !$vendacancelada)
        <div class="row">
            <div class="col-xs-12">
                <h2 class="page-header"><i class="fa fa-search"></i> Nenhuma venda encontrada!</h2>
            </div>
        </div>    
    @else
    <!-- title row -->
    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header">
                <i class="fa fa-globe"></i> Comprovante de Venda
                <small class="pull-right">Data emissão: {{$venda->dhregistro->format('d/m/Y')}}</small>
              
            </h2>
        </div>
    </div>
    <!-- info row -->
    <div class="row invoice-info">
      <!-- /.col -->
      <div class="col-sm-2 invoice-col">
        <img src="{{$venda->associado->fotourl}}" class="img img-responsive img-thumbnail" style="max-height: 140px; max-width: 140px" />
      </div>
      <div class="col-sm-6 invoice-col">
        Associado
        <address>
          <strong>{{$venda->associado->nome}}</strong><br>
          <strong>Matrícula: {{$venda->associado->id}}</strong><br>
          {{$venda->associado->enderecocompleto}}<br>
          {{$venda->associado->bairro}}<br>
          CEP: {{$venda->associado->cep}}<br>
          {{$venda->associado->telefone=='' ? '' : 'Tel: ' . $venda->associado->telefone . '<br>'}}
          {{$venda->associado->email=='' ? '' : 'E-mail: ' . $venda->associado->email}}
        </address>
      </div>
      <div class="col-sm-4 invoice-col">
            Emitido por
            <address>
                @if($venda->origem==0)
                    Usuário: <strong>{{$venda->usuario->nome}}</strong><br>
                @else
                    <strong>Pelo próprio associado</strong>
                @endif
                IP: {{$venda->ipregistro}}<br>
            </address>
          </div>
    </div>
    <!-- /.row -->

    <!-- Table row -->
    <div class="row">
      <div class="col-xs-12 table-responsive">
        <table class="table table-striped  table-condensed" border="1px">
          <thead>
          <tr>
            <th>Venda</th>
            <th>Descricao</th>
            <th class="text-center">Qtde/Usado</th>
            <th>Validade</th>
            <th><div class="text-right">Total</div></th>
          </tr>
          </thead>
          <tfoot>
                <tr class="bg-gray-light h4">
                    <th colspan="2">
                        @if($venda->idpai>0)
                        <span class="pull-left text-bold margin-r-5">
                            <a href="{{route('admin.operacional.vendas.show', [$venda->idpai])}}"><i class="fa fa-level-up" aria-hidden="true"></i> Venda mestre: {{$venda->idpai}} </a>
                        </span>
                        @endif                          
                        <div class="text-right"><small>Qtde</small></div>
                    </th>
                    <th>
                        <div class="text-center">
                            {{ $venda->qtdetotal + $venda->vendasfilho->sum('qtdetotal') }} / 
                            {{ $venda->qtdebaixa + $venda->vendasfilho->sum('qtdebaixa') }} 
                        </div>
                    </th>
                    <th><div class="text-right"><small>Total</small></div></th>
                    <th><div class="text-right">{{formatRS( $venda->total + ($venda->vendasfilho->count()==0 ? 0 : $venda->vendasfilho->sum('total') ) )}}</div></th>
                </tr>                
        </tfoot>          
          <tbody>

        @foreach ($venda->itens as $key => $item )
          <tr>
            <td>{{$venda->id}}</td>
            <td>{{$item->produto->descricao . ' - Cód.: '. $item->produto->id}}</td>
            <td><div class="text-center">{{$item->qtdetotal  . ' / ' . $item->qtdebaixa }}</div></td>
            <td><div class="text-center">{{$item->validade->format('d/m/Y')}}</div></td>
            <td><div class="text-right">{{formatRS($item->total)}}</div></td>
          </tr>
        @endforeach

        @foreach ($venda->vendasfilho as $key => $filho )
            @foreach ($filho->itens as $key => $item )
                <tr>
                    <td><a href="{{route('admin.operacional.vendas.show', [$filho->id])}}">{{$filho->id}}</a></td>
                    <td>{{$item->produto->descricao . ' - Cód.: '. $item->produto->id}}</td>
                    <td><div class="text-center">{{$item->qtdetotal  . ' / ' . $item->qtdebaixa }}</div></td>
                    <td><div class="text-center">{{$item->validade->format('d/m/Y')}}</div></td>
                    <td><div class="text-right">{{formatRS($item->total)}}</div></td>
                </tr>
            @endforeach
        @endforeach

        

          </tbody>
        </table>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row">
        <!-- accepted payments column -->
        <div class="col-xs-12">
            <p class="lead">Formas de pagamento:</p>
            <table class="table table-striped table-condensed" border="1px">
                    <thead>
                    <tr>
                    <th>Nº fatura/Venda</th>
                    <th>Descrição</th>
                    <th><div class="text-right">Valor</div></th>
                    </tr>
                    </thead>
                    <tbody>
        
                @foreach ($venda->faturamento as $key => $fatura )
                    <tr>
                    <td><a href="{{route('admin.operacional.financeiro.fatura.show', [$fatura->id])}}" >
                            {{$fatura->id . ' / ' . $venda->id}}  <i class="fa fa-edit" aria-hidden="true"></i>
                        </a>
                    </td>
                    <td>{!!$fatura->formapagto->categ . 
                            ($fatura->formapagto->pago==1 ? '' : ' - Vecto: <b>'. $fatura->vencimento->format('d/m/Y') . '</b>') .
                            ($fatura->ref=='' ? '' : '- Nº doc..: '. $fatura->ref . '</b>') 
                            !!}
                        @if($fatura->anexo<>'')
                            <a href="{{route('admin.operacional.financeiro.anexodownload', [$fatura->id, $fatura->anexo])}}" target="_blank" class="btn btn-link text-blue pull-right"><i class="fa fa-paperclip" aria-hidden="true"></i> Download anexo</a>
                        @endif
                    </td>
                    <td>
                            <div class="text-right">
                                    @if($fatura->pago==1)
                                    <span class="text-primary pull-left"><i class="fa fa-check-circle" aria-hidden="true"></i> Quitado </span>
                                @else
                                    <span class="text-danger pull-left"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Em aberto </span>
                                @endif

                                <strong>{{formatRS($fatura->valor)}}</strong>
                            </div>
                        </td>
                    </tr>
                @endforeach



                @foreach ($venda->vendasfilho as $key => $filho )
                    @foreach ($filho->faturamento as $key => $fatura )
                    <tr>
                    <td><a href="{{route('admin.operacional.financeiro.fatura.show', [$fatura->id])}}" >
                            {{$fatura->id . ' / ' . $filho->id}}  <i class="fa fa-edit" aria-hidden="true"></i>
                        </a>
                    </td>
                    <td>{!!$fatura->formapagto->categ . 
                            ($fatura->formapagto->pago==1 ? '' : ' - Vecto: <b>'. $fatura->vencimento->format('d/m/Y') . '</b>') .
                            ($fatura->ref=='' ? '' : ' - Nº doc..: '. $fatura->ref . '</b>') 
                            !!}
                        @if($fatura->anexo<>'')
                            <a href="{{route('admin.operacional.financeiro.anexodownload', [$fatura->id, $fatura->anexo])}}" target="_blank" class="btn btn-link text-blue pull-right"><i class="fa fa-paperclip" aria-hidden="true"></i> Download anexo</a>
                        @endif                            
                    </td>
                    <td>
                            <div class="text-right">
                                
                                @if($fatura->pago==1)
                                    <span class="text-primary pull-left"><i class="fa fa-check-circle" aria-hidden="true"></i> Quitado </span>
                                @else
                                    <span class="text-danger pull-left"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Em aberto </span>
                                @endif

                                <strong>{{formatRS($fatura->valor)}}</strong>
                
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @endforeach







                </tbody>
            </table>        
    

            <p class="text-red well well-sm no-shadow" style="margin-top: 10px;">
                Passagens serão liberadas somente após confirmado o pagamento da fatura referente a venda do mês.
            </p>
        </div>
    </div>

      

    <!-- this row will not appear when printing -->
    <div class="row no-print">
      <div class="col-xs-12 text-center">
          <div class="btn-group ">
                <a href="{{route('admin.operacional.vendas.print', [$venda->id])}}#autoprint" class="btn btn-success"><i class="fa fa-print"></i> Imprimir</a>
                @if($venda->idpai>0)
                <a href="{{route('admin.operacional.vendas.show', [$venda->idpai])}}" class="btn btn-default"><i class="fa fa-level-up" aria-hidden="true"></i> Venda mestre: {{$venda->idpai}} </a>

                @endif 
          </div>
        
      </div>
    </div>
</section>

<section class="invoice">
    <!-- title row -->
    <form action="{{ route('admin.operacional.vendas.cancelamento') }}" method="POST">
    {!! csrf_field() !!}
    <input type="hidden" name="id" value="{{$venda->id}}" />
    <div class="row">
        <div class="col-xs-12">
        <h2 class="page-header"><i class="fa fa-ban" ></i> Cancelamento de venda</h2>
        </div>
    </div>
    <div class="row invoice-info">
        <div class="col-xs-12">
        <div class="form-group">
            <label>Motivo do cancelamento</label>
            <input type="text" name="motivo" min="3" required class="form-control" placeholder="Informe o motivo do cancelamento">
        </div>
        </div>
    </div> 
    <div class="row invoice-info">
        <div class="col-xs-12">
            <div class="checkbox">
            <label>
                <input type="checkbox" required> Estou ciente que após excluir não é possível reverter a operação!
            </label>
            </div>
        </div>
    </div>      
    <div class="row no-print">
        <div class="col-xs-12">
            <button type="submit" class="btn btn-danger"><i class="fa fa-ban"></i> Confirmar cancelamento?</a>
        </div>
    </div>       
    </form>

    
    @endif    
    {{-- venda existe --}}
</section>    
@stop
