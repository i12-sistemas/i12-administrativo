<?php

// Route::prefix('/v1/site')->namespace('API\v1\site')->group(function () {
//     Route::post('/contato/add', 'HomeController@contatoadd')->name('api.site.contato.add');
//     //configuração
//     Route::post('/configuracao/list', 'ConfiguracaoController@getconfig')->name('api.site.configuracao.list');    
// });


Route::prefix('/')->namespace('API')->group(function () {
    Route::get('teste', 'TesteController@teste');
});


Route::prefix('/v1/webicom')->namespace('API\v1\webicom')->group(function () {
    Route::post('/clientes/list', 'ClientesController@list');
  
});

Route::prefix('/v1')->namespace('API\v1')->middleware(['cors'])->group(function () {

  Route::post('auth', 'Auth\UserAuthController@auth');
  Route::post('auth/checklogin', 'Auth\UserAuthController@checklogin');
  
    // // autenticação basicAuth tem que morrer quando todos os cliente estiverem com a versão acima da 379
    // // mudanças no delphi nos forçaram a essa mudança
    // // parametro CNPJ no post é igual no HEADER e no delphi parametros iguais não são enviados.
    // Route::post('/empresas/icom/licenca/find', 'empresas\LicencasController@find')->name('api.empresas.licenca.find')->middleware('basicAuthiCom');    
    // Route::post('/empresas/icom/licenca/setapply', 'empresas\LicencasController@setApply')->name('api.empresas.licenca.setapply')->middleware('basicAuthiCom');

    // //rota de teste para POST de logs no banco de dados da i12.
    // Route::post('/empresas/icom/logs', 'empresas\i12LogErrosController@store')->name('api.empresas.logs.store')->middleware('basicAuthiCom');


    // Route::post('/empresas/icom/update/scripts', 'empresas\ScriptsUpdateController@list')->name('api.empresas.update.scripts.list')->middleware('basicAuthiCom');
    // Route::get('/empresas/icom/update/allowed', 'empresas\ScriptsUpdateController@allowed')->name('api.empresas.update.scripts.allowed')->middleware('basicAuthiCom');

    // Route::post('/empresas/icom/contato/update', 'painelcliente\ContatosController@update')->name('api.empresas.contato.update')->middleware('basicAuthiCom');
    // Route::post('/empresas/icom/versao/update', 'EmpresasController@versaoupdate')->name('api.empresas.versao.update')->middleware('basicAuthiCom');
    // Route::post('/empresas/icom/backup/log/add', 'empresas\BackupController@logadd')->name('api.empresas.backup.log.add')->middleware('basicAuthiCom');
    // Route::post('/empresas/icom/backup/list/clientes', 'empresas\BackupController@listcliente')->name('api.empresas.backup.list.clientes')->middleware('basicAuthiCom');
    // Route::post('/empresas/icom/contato/resetpwd/external', 'painelcliente\ContatosController@changepassword_external')->name('api.empresas.contato.resetpwd.external')->middleware('basicAuthiCom');
    
    // // ---------  tem que morrer -----------------------------------------------
    // Route::post('/empresas/licenca/find', 'empresas\LicencasController@find')->name('api.empresas.licenca.find')->middleware('basicAuth');              // tem que morrer
    // Route::post('/empresas/licenca/setapply', 'empresas\LicencasController@setApply')->name('api.empresas.licenca.setapply')->middleware('basicAuth');  // tem que morrer
    // // recebe os dados de usuários do sistema icom e demais
    // Route::post('/empresas/contato/update', 'painelcliente\ContatosController@update')->name('api.empresas.contato.update')->middleware('basicAuth');   // tem que morrer
    // Route::post('/empresas/versao/update', 'EmpresasController@versaoupdate')->name('api.empresas.versao.update')->middleware('basicAuth'); // tem que morrer
    // Route::post('/empresas/backup/log/add', 'empresas\BackupController@logadd')->name('api.empresas.backup.log.add')->middleware('basicAuth');
    // // Route::post('/empresas/backup/list/clientes', 'empresas\BackupController@listcliente')->name('api.empresas.backup.list.clientes')->middleware('basicAuth'); // tem que morrer
    // Route::post('/empresas/contato/resetpwd/external', 'painelcliente\ContatosController@changepassword_external')->name('api.empresas.contato.resetpwd.external')->middleware('basicAuth');
    // // ---------  tem que morrer -----------------------------------------------
    
});


Route::prefix('/v1/icomservices')->namespace('API\v1\icomServices')->middleware(['cors', 'authicomservices'])->group(function () {
  Route::group(['prefix' => 'server'], function () {
    Route::post('/', 'i12DatabasesController@update');
  });
  
  Route::group(['prefix' => 'meiocomunicacao'], function () {
    Route::post('/verificar', 'ValidarMeioComunicacaoController@verificar');
    Route::post('/validar', 'ValidarMeioComunicacaoController@validar');
    Route::post('/zap', 'ValidarMeioComunicacaoController@zap');
  });

  Route::group(['prefix' => 'terminal'], function () {
    Route::post('/', 'i12TerminaisController@update');
  });
});

Route::prefix('/v1/admin')->namespace('API\v1\admin')->middleware(['cors', 'useradmin'], ['except' => ['tabelaibpt.download']])->group(function () {

  Route::group(['prefix' => 'servidores'], function () {
    Route::get('/', 'i12DatabasesController@index');
  });


  Route::group(['prefix' => 'backup'], function () {
    // Route::get('/', 'i12DatabasesController@index');
    
    Route::get('/', 'BackupController@list');
    Route::get('dash', 'BackupController@dash');

    Route::get('list/{diretorio}/files', 'BackupController@listfile');
    Route::get('list/{diretorio}/filespormes', 'BackupController@filespormes');
    Route::post('list/{diretorio}/sync', 'BackupController@sync');
    
    Route::delete('delete/files', 'BackupController@deletefiles');

    Route::get('download/{md5}', 'BackupController@download');
    
    Route::get('report/alert', 'BackupController@reportalert')->name('adm.backup.report.alert');
  });


  //     // backup

  Route::group(['prefix' => 'tabelaibpt'], function () {
    Route::get('old', 'TabelaIBPTController@OldFormat_index');
    Route::delete('old', 'TabelaIBPTController@OldFormat_delete');
    
    Route::get('/', 'TabelaIBPTController@index');
    Route::post('upload', 'TabelaIBPTController@upload');
    Route::delete('/', 'TabelaIBPTController@delete');
    
    Route::get('logs', 'TabelaIBPTController@logs');
    
    Route::get('download/{uf}', 'TabelaIBPTController@download')->withoutMiddleware('useradmin');
  });

  
});

// novo
// Route::prefix('/v2/painelcliente')->namespace('API\v2\painelcliente')->group(function () {
  
//   Route::get('/ticket/{id}', 'TicketController@find');
// });

//sistema antigo e migração
// Route::prefix('/v1/painelcliente')->namespace('API\v1\painelcliente')->group(function () {
//     // acesso publico - fora do csrf 
//     Route::post('/meuschamados/contadores', 'OrdemServicoController@contadoresGroupCliente')->name('api.painelcliente.chamados.contadoresGroupCliente')->middleware('BashAuthUser');
//     Route::post('/meuschamados/ultimaatualizacao', 'OrdemServicoController@ultimaatualizacao')->name('api.painelcliente.chamados.ultimaatualizacao')->middleware('BashAuthUser');


//     Route::post('/chamados/addchamado', 'OrdemServicoController@addchamado')->name('api.painelcliente.chamados.addchamado');
//     Route::post('/chamados/list', 'OrdemServicoController@list')->name('api.painelcliente.chamados.list');
//     Route::post('/chamados/contadores', 'OrdemServicoController@contadores')->name('api.painelcliente.chamados.contadores');
    
    


//     Route::post('/chamados/showdetalhe', 'OrdemServicoController@showdetalhe')->name('api.painelcliente.chamados.showdetalhe');
//     Route::post('/chamados/addmessageusuario', 'OrdemServicoController@addmessageusuario')->name('api.painelcliente.chamados.addmessageusuario');
//     Route::post('/chamados/setreadchamado', 'OrdemServicoController@setreadchamado')->name('api.painelcliente.chamados.setreadchamado');
//     Route::post('/chamados/avaliachamado', 'OrdemServicoController@avaliachamado')->name('api.painelcliente.chamados.avaliachamado');
//     Route::post('/chamados/avaliaperguntalist', 'OrdemServicoController@avaliaperguntalist')->name('api.painelcliente.chamados.avaliaperguntalist');
//     Route::post('/chamados/mensagemuploadanexo', 'OrdemServicoController@mensagemuploadanexo')->name('api.painelcliente.chamados.mensagemuploadanexo');
//     Route::post('/chamados/mensagemdownloadanexo', 'OrdemServicoController@mensagemdownloadanexo')->name('api.painelcliente.chamados.mensagemdownloadanexo');
//     Route::post('/chamados/encerrachamadousuario', 'OrdemServicoController@encerrachamadousuario')->name('api.painelcliente.chamados.encerrachamadousuario');
//     Route::post('/chamados/sendmailchamado', 'OrdemServicoController@sendmailchamado')->name('api.painelcliente.chamados.sendmailchamado');

//     Route::post('/contatos/find', 'ContatosController@find')->name('api.painelcliente.contatos.find');

//     Route::post('/configuracao/find', 'ConfiguracaoController@getconfig')->name('api.painelcliente.configuracao.find');
   
// });
