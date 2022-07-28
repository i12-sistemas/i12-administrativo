<?php

namespace App\Mail\auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\models\ContatoResetPwdTokens;
use Exception;

class ContatoResetPwdRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $token;
    protected $url;
    public function __construct($token, $url)
    {
        $this->token = ContatoResetPwdTokens::where('token', '=', $token)->first();
        $this->url = $url;
        if (!$this->token) throw new Exception('Nenhum token');
        if ($this->token->expirado) throw new Exception('token expirado');
    }

    public function build()
    {
        $content = [
          'token' => $this->token,
          'url' => $this->url
        ];
        return $this->markdown('emails.auth.contatoresetpwdrequest') //pass here your email blade file
            ->subject('Solicitação de alteração de senha - ' . env('APP_NAME'))
            ->To($this->token->username, $this->token->contato->nome)
            ->with($content);
    }
}
