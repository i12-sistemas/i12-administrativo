<?php
/*
Estrutura de rotas - Weber por Deus, mantenha padrão!!!
/                   => site / home acesso publico
/adm/             => administração
/painelcliente/     => painel do cliente
/api/               => api
 */

// Route::prefix('/adm')->namespace('adm')->group(function () {
//   Route::get('/login', 'LoginController@index')->name('adm.login');
//   Route::post('/auth', 'LoginController@login')->name('adm.auth');
//   Route::post('/logout', 'LoginController@logout')->name('adm.logout');

//   Route::prefix('/')->middleware('adm')->group(function () {
//     Route::get('/', 'HomeController@index')->name('adm.home');
//     // scripts update
//     Route::get('/scripts', 'ScriptUpdateController@index')->name('adm.scripts.listagem');
//     Route::get('/scripts/add', 'ScriptUpdateController@add')->name('adm.scripts.add');
//     Route::post('/scripts/upload', 'ScriptUpdateController@upload')->name('adm.scripts.upload');
//     Route::get('/scripts/delete/{md5}', 'ScriptUpdateController@delete')->name('adm.scripts.delete');
//     Route::post('/scripts/deletemulti', 'ScriptUpdateController@deletemulti')->name('adm.scripts.delete.multi');

//     //log daniel
//     Route::get('/icom/logs', 'i12LogErrosController@index')->name('adm.logs.index');
//     Route::get('/icom/logs/clientes', 'i12LogErrosController@listaclientes')->name('adm.logs.clientes');
//     Route::get('/icom/logs/{cnpj}', 'i12LogErrosController@logsclientes')->name('adm.logclientes.logs');
//     // backup
//     Route::get('/backup/dash', 'BackupController@dash')->name('adm.backup.dash');
//     Route::get('/backup/list', 'BackupController@list')->name('adm.backup.list');
//     Route::get('/backup/report/alert', 'BackupController@reportalert')->name('adm.backup.report.alert');
//     Route::get('/backup/list/{diretorio}/files', 'BackupController@listfile')->name('adm.backup.list.files');
//     Route::get('/backup/list/{diretorio}/sync', 'BackupController@sync')->name('adm.backup.list.sync');
//     Route::post('/backup/delete', 'BackupController@deletefiles')->name('adm.backup.delete');
//     Route::get('/backup/download/{md5}', 'BackupController@download')->name('adm.backup.download');
//     // clientes
//     Route::get('/clientes/licencas', 'ClientesController@licencas')->name('adm.clientes.licencas');
//     // etiquetas
//     Route::get('/etiquetas', 'EtiquetasController@index')->name('adm.etiquetas');
//     Route::get('/etiquetas/delete/{md5}', 'EtiquetasController@delete')->name('adm.etiquetas.delete');
//     Route::post('/etiquetas/deletemulti', 'EtiquetasController@deletemulti')->name('adm.etiquetas.delete.multi');
//     Route::get('/etiquetas/add', 'EtiquetasController@add')->name('adm.etiquetas.add');
//     Route::post('/etiquetas/upload', 'EtiquetasController@upload')->name('adm.etiquetas.upload');
//     //tabela IBPT
//     Route::get('/tabelaibpt', 'TabelaIBPTController@index')->name('adm.tabelaibpt');
//     Route::get('/tabelaibpt/add', 'TabelaIBPTController@add')->name('adm.tabelaibpt.add');
//     Route::post('/tabelaibpt/upload', 'TabelaIBPTController@upload')->name('adm.tabelaibpt.upload');
//     Route::get('/tabelaibpt/delete/{filename}', 'TabelaIBPTController@delete')->name('adm.tabelaibpt.delete');
//   });
// });

// // Auth::routes();
// Route::prefix('/painelcliente')->namespace('painelcliente')->group(function () {
//   Route::prefix('/icom')->namespace('icom')->group(function () {
//     Route::get('/etiquetas', 'EtiquetasController@index');
//     Route::get('/etiquetas/download/{md5filename}', 'EtiquetasController@download')->name('etiquetas.download');

//     Route::get('/etiquetas/download/{md5filename}', 'EtiquetasController@download')->name('etiquetas.download');
//   });     

//   Route::get('login', 'SpaController@index')->name('painelcliente.login');

//   Route::get('login/automatico/{usuario}/{cnpj}/{hash}', 'Auth\LoginController@autoauth')->name('painelcliente.login.auto');
//   //antigo metodo desativar em 01/01/2020
//   Route::post('login/createtoken', 'Auth\LoginController@autoauthcreatetoken')->name('painelcliente.login.auto.autoauthcreatetoken');
//   //novo  metodo
//   Route::post('login/createtokenrelacionamento', 'Auth\LoginController@autoauthcreatetokenrelacionamento')->name('painelcliente.login.auto.autoauthcreatetokenrelacionamento');

  
//   Route::post('login', 'Auth\LoginController@auth')->name('painelcliente.login.auth');
//   Route::post('login/listempresas', 'Auth\LoginController@listempresas')->name('painelcliente.login.listempresas');
//   Route::post('login/setempresa', 'Auth\LoginController@setempresa')->name('painelcliente.login.setempresa');
  
//   Route::get('logout', 'Auth\LoginController@logout')->name('painelcliente.logout');
//   Route::post('logout', 'Auth\LoginController@logout')->name('painelcliente.logout');
//   Route::get('esqueciminhasenha', 'Auth\LoginController@esqueciminhasenha')->name('painelcliente.esqueciminhasenha');
//   Route::get('trocarsenha/{token}', 'Auth\LoginController@esqueciminhasenhatoken')->name('painelcliente.esqueciminhasenhatoken');
//   Route::post('resetpassword', 'Auth\LoginController@resetpassword')->name('painelcliente.resetpassword');
//   Route::post('resetpasswordconfirm', 'Auth\LoginController@resetpasswordconfirm')->name('painelcliente.resetpasswordconfirm');
//   Route::get('confirmaremail/{token}', 'Auth\LoginController@confirmaremail')->name('painelcliente.confirmaremail');

//   Route::get('', 'SpaController@index');
//   Route::get('chamado/show/publico/{idbase64}/{idhashmd5}/{email}/{emailmd5}', 'SpaController@linkpublicopainel');
  
//   Route::get('/{any}', 'SpaController@index')->where('any', '.*');      
// });


Route::get('/', function () {
  return view('welcome');
});
