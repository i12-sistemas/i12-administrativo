<?php

namespace App\Jobs\auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\models\ContatoResetPwdTokens;
use App\Mail\auth\ContatoResetPwdRequestMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\TopZapiChatController;
use Illuminate\Support\Str;

class ContatoResetPwdRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $token;

    public function __construct(ContatoResetPwdTokens $token)
    {
        $this->token = $token;
    }

    public function handle()
    {
        $urlbase = '#/resetpwd/change?username=#&token=#&code=#';
        
        $params = [
          env('APP_URL_CLIENTE', 'http://APP_URL_CLIENTE'),
          urlencode(base64_encode($this->token->username)),
          urlencode($this->token->token),
          urlencode($this->token->codenumber)
        ];

        $url = Str::replaceArray('#', $params, $urlbase);
        $field = isemail($this->token->username) ? 'email' : 'whatsapp';

        if ($field === 'email') {
          Mail::send(new ContatoResetPwdRequestMail($this->token->token, $url));
        } else {

          $zap = new TopZapiChatController();

          $msg  = 'Segue seu código de redefinição de senha do ' .  env('APP_NAME') .  PHP_EOL . 
                  '*👉 ' . $this->token->codenumber . ' 👈*' . PHP_EOL . PHP_EOL .
                  'Este código expira em uma hora ' . PHP_EOL .
                  'você também pode alterar sua senha clicando no link abaixo ' . PHP_EOL . PHP_EOL . 
                  $url . PHP_EOL . PHP_EOL . 
                  'Não é necessário responder essa mensagem';

          $zap->sendText($this->token->username, $msg);
        }
    }
}
