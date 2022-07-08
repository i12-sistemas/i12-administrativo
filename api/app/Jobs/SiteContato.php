<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use App\Mail\site\Contato;

class SiteContato implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nome;
    protected $telefone;
    protected $email;
    protected $assunto;
    protected $mensagem;
    protected $empresa;
    protected $destinatario;

    public function __construct($nome, $telefone, $email, $assunto, $mensagem, $destinatario, $empresa)
    {
        $this->nome = $nome;
        $this->telefone = $telefone;
        $this->email = $email;
        $this->assunto = $assunto;
        $this->mensagem = $mensagem;
        $this->destinatario = $destinatario;
        $this->empresa = $empresa;
    }
    public function handle()
    {
        $tipodest = (isset($this->destinatario->tipo) ? $this->destinatario->tipo : 'admin');
        if($tipodest!='user') $tipodest='admin';

        $dados = [ 'tipodest' => $tipodest,
            'nome' => $this->nome,
            'telefone' => $this->telefone,
            'email' => $this->email,
            'assunto' => $this->assunto,
            'mensagem' => $this->mensagem,
            'destinatario' => $this->destinatario,
            'empresa' => $this->empresa,
        ];
        Mail::send(new Contato($dados));
    }
}
