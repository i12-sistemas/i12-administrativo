<?php

namespace App\Mail\site;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PrematriculaLinkConfirmacao extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function build()
    {
        $content = $this->content;
        $prematricula = $content['prematricula'];
        $empresa = $content['empresa'];

        return $this->markdown('site.mail.prematriculaconfirmacao') //pass here your email blade file
            ->subject('Link para confirmação de pré-matricula - ' . $empresa->fantasia)
            ->To($prematricula->email, $prematricula->nome)
            ->with($content);
    }
}
