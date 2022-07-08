<?php

namespace App\Mail\painelcliente;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function build()
    {
        $content = $this->content;
        $associado = $content['associado'];
        $empresa = $content['empresa'];

        return $this->markdown('usuario.auth.mail.resetpassword') //pass here your email blade file
            ->subject('Solicitação de reset de senha - ' . $empresa->fantasia)
            ->To($associado->email, $associado->nome)
            ->with($content);
    }
}
