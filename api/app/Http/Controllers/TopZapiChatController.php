<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;

class TopZapiChatController extends Controller
{
  private  $tokenapi;
  private  $responsecode;

  function __construct()
  {
      $this->tokenapi = env('TOPZAPICHAT_TOKEN', '');
      if ($this->tokenapi ? $this->tokenapi === '' : true) throw new Exception('Nenhum token informada para envio de WhatsApp (TopZapi)');
  }

  public function sendText($number, $msg){
    $body = [
      'number' => $number,
      'message' => $msg,
      'forceSend' => true,
      'verifyContac' => false
    ];
    $response = Http::withHeaders([
          'access-token' => $this->tokenapi,
    ])->post('https://api.topzapichat.com.br/core/v2/api/chats/send-text', $body);

    if ($response->successful()) {
      return true;
    } 
    // Determine if the response has a 400 level status code...
    if ($response->failed()) {
      $json = $response->json();
      $error = 'Error: ' . $response->status();
      if (isset($json['msg'])) $error = $error . ' - ' .  $json['msg'];
      if (isset($json['errorCode'])) $error = $error .  ' - errorCode: '. $json['msg'];
      throw new Exception($error);
    } 
  }
}
