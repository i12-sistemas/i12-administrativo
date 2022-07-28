<?php

namespace App\Mail\icomServices;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\models\i12ChecagemMeioComunicacao;
use Exception;

class ValidacaoMeioComunicacaoMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $meiocomunicacao;
    protected $url;
    public function __construct($id, $url)
    {
        $this->meiocomunicacao = i12ChecagemMeioComunicacao::find($id);
        $this->url = $url;
        if (!$this->meiocomunicacao) throw new Exception('Nenhum meio de comunicação encontrado com o id ' . $id);
        if ($this->meiocomunicacao->expirado) throw new Exception('Meio de comunicação expirado');
    }

    public function build()
    {
        $content = [
          'meiocomunicacao' => $this->meiocomunicacao,
          'url' => $this->url
        ];
        return $this->markdown('emails.icomservices.validacaoemail') //pass here your email blade file
            ->subject($this->meiocomunicacao->subject)
            ->To($this->meiocomunicacao->chave, $this->meiocomunicacao->from)
            ->with($content);
    }
}
