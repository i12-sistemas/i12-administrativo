<?php

namespace App\Mail\painelcliente;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\models\OrdemServico;
use App\models\Interacao;

class InteracaoAddClienteMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $atendimento;
    protected $interacao;
    public $email;
    public $subject;

    public function __construct(OrdemServico $atendimento, Interacao $interacao, $email, $subject)
    {
        $this->atendimento = $atendimento;
        $this->interacao = $interacao;
        $this->email = $email;
        $this->subject = $subject;
    }

    public function build()
    {
      $subject  = env('APP_NAME') . ' - ' . $this->subject . ' - Atendimento #' . $this->atendimento->id . ' - Cliente: ' . $this->atendimento->cliente->nome;
      $content = [
        'atendimento' => $this->atendimento,
        'interacao' => $this->interacao
      ];

      return $this->markdown('painelcliente.mail.novainteracaodocliente') //pass here your email blade file
          ->subject($subject)
          ->To($this->email)
          ->with($content);
    }
}
