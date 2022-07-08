@extends('layouts.admin.templatereport')

@section('title', $title or '')

@section('content')
<section class="invoice">
    <div class="row no-print">
        <div class="col-xs-12 text-center">
            <div class="btn-group">
                <button onclick="window.print();" class="btn btn-success"><i class="fa fa-print"></i> Imprimir</button>
                <button onclick="window.close()" class="btn btn-default"><i class="fa fa-close"></i> Fechar</a>
            </div>
            
        </div>
    </div>
          

    <!-- title row -->
    <div class="row">
      <div class="col-xs-12">
        <h2 class="page-header">
          <i class="fa fa-globe"></i> Comprovante de Venda
          <small class="pull-right">Data emissão: {{$venda->dhregistro->format('d/m/Y')}}</small>
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
      <div class="col-sm-6 invoice-col">
        Emitido por
        <address>
          <strong>{{$empresa->fantasia}}</strong><br>
          {{$empresa->razao}}<br>
          {{$empresa->endereco . ', ' . $empresa->nro }}<br>
          {{$empresa->bairro . ($empresa->complemento=='' ? '' : ' - ' . $empresa->complemento)}}<br>
          CEP: {{$empresa->cep}}<br>
          Tel: {{$empresa->telefone}}<br>
          E-mail: {{$empresa->email}}
        </address>
      </div>
      <!-- /.col -->
      <div class="col-sm-6 invoice-col">
        Para
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
      <!-- /.col -->
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
            <th>Validade</th>
            <th><div class="text-right">Total</div></th>
          </tr>
          </thead>
          <tfoot>
                <tr>
                    <th colspan="3"><div class="text-right">Total</div></th>
                    <th><div class="text-right">{{formatRS( $venda->total + ($venda->vendasfilho->count()==0 ? 0 : $venda->vendasfilho->sum('total') ) )}}</div></th>
                </tr>                
        </tfoot>          
          <tbody>

        @foreach ($venda->itens as $key => $item )
          <tr>
            <td>{{$venda->id}}</td>
            <td>{{$item->produto->descricao . ' - Cód.: '. $item->produto->id}}</td>
            <td>{{$item->validade->format('d/m/Y')}}</td>
            <td><div class="text-right">{{formatRS($item->total)}}</div></td>
          </tr>
        @endforeach

        @foreach ($venda->vendasfilho as $key => $filho )
            @foreach ($filho->itens as $key => $item )
                <tr>
                    <td>{{$filho->id}}</td>
                    <td>{{$item->produto->descricao . ' - Cód.: '. $item->produto->id}}</td>
                    <td>{{$item->validade->format('d/m/Y')}}</td>
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
                    <td>{{$fatura->id . ' / ' . $venda->id}}</td>
                    <td>{!!$fatura->formapagto->categ . 
                            ($fatura->formapagto->pago==1 ? '' : ' - Vecto: <b>'. $fatura->vencimento->format('d/m/Y') . '</b>') .
                            ($fatura->ref=='' ? '' : '- Nº doc..: '. $fatura->ref . '</b>') 
                            !!}</td>
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
                    <td>{{$fatura->id . ' / ' . $filho->id}}</td>
                    <td>{!!$fatura->formapagto->categ . 
                            ($fatura->formapagto->pago==1 ? '' : ' - Vecto: <b>'. $fatura->vencimento->format('d/m/Y') . '</b>') .
                            ($fatura->ref=='' ? '' : ' - Nº doc..: '. $fatura->ref . '</b>') 
                            !!}</td>
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
            <button onclick="window.print();" class="btn btn-success"><i class="fa fa-print"></i> Imprimir</button>
            <button onclick="window.close()" class="btn btn-default"><i class="fa fa-close"></i> Fechar</a>
          </div>
      </div>
    </div>

</section>
@stop

@section('js')
<script>
if(window.location.hash) {
      var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
      if(hash=='autoprint')  window.print();
  };


</script>
@stop