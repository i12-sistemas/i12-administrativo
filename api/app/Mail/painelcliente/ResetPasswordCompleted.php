<?php

namespace App\Mail\painelcliente;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordCompleted extends Mailable
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

        return $this->markdown('usuario.auth.mail.resetpasswordcompleted') //pass here your email blade file
            ->subject('Informativo de senha alterada!')
            ->To($associado->email, $associado->nome)
            ->with($content);
    }
}
