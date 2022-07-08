@extends('site.layout.template1')
@section('title', '')
@section('content')


<section id="suporte_chat" >

    <div class="chatintegrado-box">
      <div id='tawk_5a6a20d44b401e45400c65e9'></div>
    </div>

    <div class="chatintegrado-box-regras">
        <div class="chatintegrado-logo">
          <img src="{{ asset('vendor/templatesite/images/logo2.png') }}" alt="i12 Sistemas" />
        </div>     
        <hr>     
        <p style="font-weight: bold">Informações importantes</p>
        <ul class="list-group">
            <li class="list-group-item">Identifique-se de maneira correta.</li>
            <li class="list-group-item">Chat sem interação por mais de 2 minutos será encerrado automaticamente.</li>
            <li class="list-group-item">Não abandone o operador de suporte, ele precisará de respostas pra prestar suporte adequado.</li>
            <li class="list-group-item">A equipe de suporte não retorna ligações, se deseja um suporte por telefone, por favor, ligue em um de nossos números: <br>(16) 3851-6983<br>(16) 3851-3621<br>(16) 9 9162-4912</li>
            <li class="list-group-item">Em caso de Chat Offline, registre um chamado ou envie e-mail para <a href="mailto:suporte@i12.com.br">suporte@i12.com.br</a></li>
        </ul>
  
    </div>        
  </div>

</section>
@stop