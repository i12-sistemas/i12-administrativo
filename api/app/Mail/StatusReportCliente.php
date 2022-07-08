<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StatusReportCliente extends Mailable
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
        $iduser = $content['iduser'];

        return $this->markdown('admin.cadastros.associados.mail.sendlogin') //pass here your email blade file
            ->subject('Status Report - ' . $associado->nome . ' - ' . $associado->id)
            ->To('weberdepaula@gmail.com', 'Weber de Paula')
            ->ReplyTo('weber.paula@idoze.com.br', 'Sistema i12')
            ->with($content);
    }
}
