<?php

namespace App\Jobs\icomServices;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\models\i12ChecagemMeioComunicacao;
use App\Mail\icomServices\ValidacaoMeioComunicacaoMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\TopZapiChatController;
use Illuminate\Support\Str;

class ValidacaoMeioComunicacaoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $meiocomunicacao;

    public function __construct(i12ChecagemMeioComunicacao $meiocomunicacao)
    {
        $this->meiocomunicacao = $meiocomunicacao;
    }

    public function handle()
    {
      $url = '';
      if ($this->meiocomunicacao->sendlink === 1) {
        $urlbase = '#/public/validacao/#?chave=#&token=#&code=#';
        
        $params = [
          env('APP_URL_ADMIN', 'http://APP_URL_ADMIN'),
          urlencode($this->meiocomunicacao->tipo),
          urlencode(base64_encode($this->meiocomunicacao->chave)),
          urlencode($this->meiocomunicacao->id),
          urlencode($this->meiocomunicacao->code)
        ];

        $url = Str::replaceArray('#', $params, $urlbase);
      }
      if ($this->meiocomunicacao->tipo === 'email') {
        Mail::send(new ValidacaoMeioComunicacaoMail($this->meiocomunicacao->id, $url));
      } else {

        $zap = new TopZapiChatController();

        $msg  = 'Segue seu código de validação:' . PHP_EOL . 
                '*👉 ' . $this->meiocomunicacao->code . ' 👈*' . PHP_EOL . PHP_EOL .
                (($this->meiocomunicacao->from ? $this->meiocomunicacao->from !== '' : false) ?  'De: * ' . $this->meiocomunicacao->from . '*' . PHP_EOL  : '' ) . 
                (($this->meiocomunicacao->subject ? $this->meiocomunicacao->subject !== '' : false) ? 'Assunto: *' . $this->meiocomunicacao->subject . '*' . PHP_EOL : '' ) . 
                (($this->meiocomunicacao->message ? $this->meiocomunicacao->message !== '' : false) ?  'Mensagem: *' . $this->meiocomunicacao->message . '*' .  PHP_EOL  : '' ) . 
                PHP_EOL . '🤖 Esta é uma mensagem de validação do seu número de contato.' .  PHP_EOL . PHP_EOL .
                PHP_EOL . '◽ Não é necessário responder essa mensagem' . PHP_EOL .
                '◽ Este código é válido até as ' . $this->meiocomunicacao->expire_at->format('H:i');

        if ($this->meiocomunicacao->sendlink === 1) {
          $msg = $msg . PHP_EOL . PHP_EOL . 'você também pode validar seu número clicando no link abaixo ' . PHP_EOL . PHP_EOL . $url;
        }
            
        $zap->sendText($this->meiocomunicacao->chave, $msg);
      }
    }
}
