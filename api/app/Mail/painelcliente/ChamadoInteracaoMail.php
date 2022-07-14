<?php

namespace App\Mail\painelcliente;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChamadoInteracaoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function build()
    {
        $content = $this->content;
        // $chamado = $content['chamado'];
        // $destinatario = $content['destinatario'];
        // $empresa = $content['empresa'];
        // $assunto = $content['assunto'];
        

        return $this->markdown('painelcliente.mail.chamadosinteracao') //pass here your email blade file
            ->subject($empresa->apelido . ' [#' . $chamado->id . '] ' . $assunto)
            ->To($destinatario->email, $destinatario->nome)
            ->with($content);
    }
}
