<?php

namespace App\Mail\site;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Contato extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function build()
    {
        $content = $this->content;
        $email = $content['email'];
        $assunto = $content['assunto'];
        $nome = $content['nome'];
        $destinatario = $content['destinatario'];
        $empresa = $content['empresa'];
        $tipodest = $content['tipodest'];

        if($tipodest=='user'){
            return $this->markdown('site.mail.contatousuario') //pass here your email blade file
                ->subject('Registro de contato - ' . $empresa->fantasia)
                ->To($destinatario->email, $destinatario->nome)
                ->with($content);
        }else{
            return $this->markdown('site.mail.contatoadmin') //pass here your email blade file
                ->subject('Registro de contato através do site - Sistema iStu - ' . $assunto)
                ->To($destinatario->email, $destinatario->nome)
                ->ReplyTo($email, $nome)
                ->with($content);
        }
    }
}
