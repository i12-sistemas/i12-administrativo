<?php

namespace App\Jobs\painelcliente;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\painelcliente\InteracaoAddClienteMail;
use Illuminate\Support\Facades\Mail;

use App\models\OrdemServico;
use App\models\Interacao;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;




class InteracaoAddClienteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $atendimento;
    public $interacao;
    public $email;
    public $subject;

    public function __construct($atendimentoid, $interacaoid, $subject)
    {
      $this->subject = $subject;
      $this->atendimento = OrdemServico::find($atendimentoid);
      if (!$this->atendimento) throw new Exception("Nenhum atendimento encontrado com o número " . $atendimentoid);
      $this->interacao = Interacao::find($interacaoid);
      if (!$this->interacao) throw new Exception("Nenhuma interação encontrada com o Id " . $interacaoid);
      
      try {
        if ($this->interacao->origem !== 'C') throw new Exception("Somente interação do cliente para o suporte");

        $contato = $this->atendimento->contato;
        if (!$contato) throw new Exception("Nenhum contato associado ao atendimento");
        // if (!$contato->emailvalidado && !$contato->whatsappvalidado) throw new Exception("Contato não tem nenhuma método de comunicação validado");
        if (!$contato->emailvalidado) throw new Exception("Contato não e-mail validado");

        $this->email = $contato->emailprincipal;

      } catch (\Throwable $th) {
        // registra erro
        try{
          // DB::beginTransaction();
          $this->interacao->emailstatus = 2;
          $this->interacao->emailerror = $th->getMessage();
          $this->interacao->emaildhprocessado = Carbon::now();
          $this->interacao->save();
          // DB::commit();
        } catch (Throwable $e) {
          // DB::rollBack();
          throw new Exception($e->getMessage());
        }   
        throw new Exception($th->getMessage());
      }
    }

    public function handle()
    {
      Mail::send(new InteracaoAddClienteMail($this->atendimento, $this->interacao, $this->email, $this->subject));
    }

    public function failed(Exception $exception)
    {
      try{
        DB::beginTransaction();
        $this->interacao->emaillist = $this->email;
        $this->interacao->emailstatus = 2;
        $this->interacao->emailerror = $exception->getMessage();
        $this->interacao->emaildhprocessado = Carbon::now();
        $this->interacao->save();
        DB::commit();
      } catch (Exception $e) {
        DB::rollBack();
        \Log::error('InteracaoAddClienteJob failed: ' . $e->getMessage());
      }    
    }
}
