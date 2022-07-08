<?php

namespace App\Jobs\painelcliente;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\models\Empresa;
use App\Mail\painelcliente\ChamadoInteracaoMail;
use Illuminate\Support\Facades\Mail;


class ChamadosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $chamado;
    protected $ultimainteracao;
    protected $destinatario;
    protected $assunto;

    public function __construct($chamado, $ultimainteracao, $destinatario, $assunto)
    {
        $this->chamado = $chamado;
        $this->ultimainteracao = $ultimainteracao;
        $this->destinatario = $destinatario;
        $this->assunto = $assunto;
    }

    public function handle()
    {
        $empresa = Empresa::first();
        $content = ['chamado' => $this->chamado,
                    'ultimainteracao' => $this->ultimainteracao,
                    'empresa' => $empresa,
                    'assunto' => $this->assunto,
                    'destinatario' => $this->destinatario,
             ];
        
        Mail::send(new ChamadoInteracaoMail($content));
    }
}
