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
      if ($this->meiocomunicacao->tipo === 'email') {
        Mail::send(new ValidacaoMeioComunicacaoMail($this->meiocomunicacao->id));
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
        $zap->sendText($this->meiocomunicacao->chave, $msg);
      }
    }
}
