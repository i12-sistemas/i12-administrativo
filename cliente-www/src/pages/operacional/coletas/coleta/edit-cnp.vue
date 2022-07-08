<template>
<div>
  <q-card :bordered="mode == 'integrado'" flat :class="$q.platform.is.mobile ? 'no-padding' : ''">
    <q-card-section v-if="loading || saving" style="padding: 0">
        <div class="fit row wrap text-center q-mt-lg q-pa-lg" v-if="loading">
          <div class="col-12">
            <q-spinner color="accent" size="3rem" :thickness="5"/>
          </div>
          <div class="col-12 q-pa-lg text-accent ">
            Consultando dados, aguarde!
          </div>
        </div>
    </q-card-section>
    <q-card-section  horizontal v-if="!loading && (!adding && !dataset.id)" >
      <div class="full-width text-center q-pa-lg">
          <div class="text-h6 q-pa-md">Nenhum registro encontrado!</div>
          <q-btn label="Voltar para consultas"  outline  :to="{ name: 'cadastro.clientes' }" />
      </div>
    </q-card-section>
    <q-separator class="q-pa-0 q-ma-0" v-if="!loading && (adding || (!adding && dataset.id > 0))" />
    <q-card-section v-if="!loading && (adding || (!adding && dataset.id > 0))" class="no-padding">
        <!-- coleta encerrada  -->
        <div class="col-12 q-mb-md"  v-if="!loading && (!adding) && (dataset.id > 0) && (dataset.situacao.value === '2')">
          <q-card class="bg-green-1 text-white" flat >
            <q-toolbar class="bg-green">
              <q-avatar icon="check_circle" text-color="white" />
              <q-toolbar-title>
                Coleta baixada/encerrada!
              </q-toolbar-title>
            </q-toolbar>
            <q-card-section>
              <div class="fit row wrap justify-start items-start content-start text-black q-col-gutter-md" >
                <div class="col-md-4">Data da baixa: <span class="text-weight-bold"> {{$helpers.datetimeToBR(dataset.dhbaixa)}}</span></div>
                <div class="col-md-4" v-if="dataset.encerramentotipo.value !== '2'">Baixado por: <span class="text-weight-bold">{{dataset.updated_usuario.id > 0 ? dataset.updated_usuario.nome : '-'}}</span></div>
                <div class="col-md-4">Tipo de baixa: <span class="text-weight-bold">{{dataset.encerramentotipo}}</span></div>
                <div class="col-12" v-if="dataset.ctenumero">Nº CT-e: <span class="text-weight-bold">{{dataset.ctenumero}}</span></div>
                <div class="col-12" v-if="dataset.justsituacao ? dataset.justsituacao !== '' : false"><span class="text-weight-bold" style="white-space: pre-line;">Justificativa: </span> <span style="white-space: pre-line;">{{dataset.justsituacao}}</span></div>

              </div>
            </q-card-section>
          </q-card>
        </div>
        <!-- coleta encerrada  -->

        <!-- coleta cancelada  -->
        <div class="col-12 q-mb-md"  v-if="!loading && (!adding) && (dataset.id > 0) && (dataset.situacao.value === '3')">
          <q-card class="bg-red-1 text-white" flat >
            <q-toolbar class="bg-red-7">
              <q-avatar :icon="dataset.situacao.icon" text-color="white" />
              <q-toolbar-title>
                Coleta cancelada!
              </q-toolbar-title>
            </q-toolbar>
            <q-card-section>
                <div class="row text-black" >
                  <div class="col-md-12"><span class="text-weight-bold">Data do cancelamento:</span> {{$helpers.datetimeToBR(dataset.dhbaixa)}}</div>
                  <div class="col-md-12"><span class="text-weight-bold">Cancelado por:</span> {{dataset.updated_usuario.id > 0 ? dataset.updated_usuario.nome : '-'}}</div>
                  <div class="col-md-12"><span class="text-weight-bold">Justificativa:</span> {{dataset.justsituacao}}</div>
                  <div class="col-md-12"><span class="text-weight-bold">Tipo de cancelamento:</span> {{dataset.encerramentotipo}}</div>
                </div>
            </q-card-section>
          </q-card>
        </div>
        <!-- coleta cancelada  -->

        <!-- origem -->
        <!-- 1=interno direto, 2=interno orcamento, 3=painel do cliente, 4=Coleta avulsa aplicativo -->
        <div class="col-12 q-mb-md" v-if="!loading && (dataset ? dataset.origem.value != '1' : false) ">
          <q-card class="" bordered flat >
            <q-toolbar class="bg-grey-3 text-black">
              <q-avatar :icon="dataset.origem.icon" text-color="black" />
              <q-toolbar-title class="text-subtitle2">
                Origem da coleta :: {{ dataset.origem.description}}
              </q-toolbar-title>
            </q-toolbar>
            <q-card-section class="no-padding" v-if="dataset.origem.value === '2' && dataset.orcamentoid > 0">
              <q-card flat>
                <q-card-section >
                  <div class="fit row wrap">
                    <div class="col-12">
                      Gerada do orçamento #{{dataset.orcamentoid}}
                    </div>
                  </div>
                </q-card-section>
              </q-card>
            </q-card-section>
          </q-card>
        </div>
        <!-- origem -->

        <!-- chave -->
        <div class="col-12 q-mb-md" v-if="!loading && dataset">
          <q-card class="" bordered flat >
            <q-toolbar class="bg-grey-3">
              <q-avatar icon="info" />
              <q-toolbar-title class="text-subtitle2">
                Nota Fiscal
              </q-toolbar-title>
            </q-toolbar>
            <q-card-section class="no-padding">
              <q-card flat>
                <q-card-section>
                  <div class="fit row wrap q-col-gutter-x-md q-col-gutter-y-md q-pb-md">
                    <div class="col-12">
                      <q-field label="Chave da Nota Fiscal" stack-label outlined :dense="!$q.platform.is.mobile" >
                        <template v-slot:control>
                          <div class="self-center full-width no-outline" tabindex="0">{{dataset.chavenota}}</div>
                        </template>
                      </q-field>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                      <q-field label="Número da Nota" stack-label outlined :dense="!$q.platform.is.mobile" >
                        <template v-slot:control>
                          <div class="self-center full-width no-outline" tabindex="0">{{dataset.nfe.nNF}}</div>
                        </template>
                      </q-field>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                      <q-field label="Mês/Ano Emissão" stack-label outlined :dense="!$q.platform.is.mobile" >
                        <template v-slot:control>
                          <div class="self-center full-width no-outline" tabindex="0">{{dataset.nfe.mesAno}}</div>
                        </template>
                      </q-field>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <q-field label="CNPJ Emissor" stack-label outlined :dense="!$q.platform.is.mobile" >
                        <template v-slot:control>
                          <div class="self-center full-width no-outline" tabindex="0">{{$helpers.mascaraDocCPFCNPJ(dataset.nfe.CNPJ)}}</div>
                        </template>
                      </q-field>
                    </div>
                  </div>
                </q-card-section>
              </q-card>
            </q-card-section>
          </q-card>
        </div>
        <!-- chave -->

        <!-- cliente de origem -->
        <div class="col-12 q-mb-md" v-if="!loading && dataset">
          <q-card class="" bordered flat >
            <q-toolbar class="bg-grey-3 text-black" si>
              <q-avatar icon="trip_origin" text-color="black" />
              <q-toolbar-title class="text-subtitle2">
                Remetente :: {{temclienteorigem ? dataset.clienteorigem.razaosocial : 'Não informado!'}}
              </q-toolbar-title>
              <q-btn color="accent" flat label="em aberto" stretch :loading="checkingemaberto" v-if="!loading && adding && temclienteorigem" @click="openEmAbertos" :disable="!checkingemaberto && coletasemabertoremetente === 0 " >
                <q-badge color="accent" floating>{{coletasemabertoremetente}}</q-badge>
              </q-btn>
            </q-toolbar>
            <q-card-section class="no-padding">
              <q-card flat>
                <q-card-section >
                  <div class="fit row wrap">
                    <div class="col-12">
                      <q-field label="Chave da Nota Fiscal" stack-label outlined :dense="!$q.platform.is.mobile" >
                        <template v-slot:control>
                          <div class="self-center full-width no-outline" tabindex="0">{{dataset.clienteorigem.razaosocial}}</div>
                        </template>
                      </q-field>
                    </div>
                  </div>
                  <div class="fit row wrap q-pt-md" v-if="temclienteorigem" >
                    <div class="col-md-12 col-xs-12">
                      <div class="fit row wrap">
                        <div class="col-md-12">
                          <div class="full-width row inline  justify-between items-start content-start">
                            <div class="col-md-10">CNPJ: {{$helpers.mascaraDocCPFCNPJ(dataset.clienteorigem.cnpj)}}</div>
                          </div>
                          <q-separator spaced />
                          <div class="row">
                              <div class="col-md-4"><span class="text-weight-bold">Telefone</span></div>
                              <div class="col-md-4">{{$helpers.mascaratel(dataset.clienteorigem.fone1) }}</div>
                              <div class="col-md-4">{{$helpers.mascaratel(dataset.clienteorigem.fone2) }}</div>
                          </div>
                          <div class="row" v-if="dataset.clienteorigem.temhorariosegqui()">
                              <div class="col-md-4"><span class="text-weight-bold">Seg à Qui</span></div>
                              <div class="col-md-4" v-if="dataset.clienteorigem.segqui_hr1_i || dataset.clienteorigem.segqui_hr1_f">{{ $helpers.ifnull(dataset.clienteorigem.segqui_hr1_i, '-') + ' à ' + $helpers.ifnull(dataset.clienteorigem.segqui_hr1_f, '-') }}</div>
                              <div class="col-md-4" v-if="dataset.clienteorigem.segqui_hr2_i || dataset.clienteorigem.segqui_hr2_f">{{ $helpers.ifnull(dataset.clienteorigem.segqui_hr2_i, '-') + ' à ' + $helpers.ifnull(dataset.clienteorigem.segqui_hr2_f, '-') }}</div>
                          </div>
                          <div class="row" v-if="dataset.clienteorigem.temhorariosex()">
                              <div class="col-md-4"><span class="text-weight-bold">Sexta</span></div>
                              <div class="col-md-4" v-if="dataset.clienteorigem.sex_hr1_i || dataset.clienteorigem.sex_hr1_f">{{ $helpers.ifnull(dataset.clienteorigem.sex_hr1_i, '-') + ' à ' + $helpers.ifnull(dataset.clienteorigem.sex_hr1_f, '-') }}</div>
                              <div class="col-md-4" v-if="dataset.clienteorigem.sex_hr2_i || dataset.clienteorigem.sex_hr2_f">{{ $helpers.ifnull(dataset.clienteorigem.sex_hr2_i, '-') + ' à ' + $helpers.ifnull(dataset.clienteorigem.sex_hr2_f, '-') }}</div>
                          </div>
                          <div class="row" v-if="dataset.clienteorigem.temhorarioportaria()">
                              <div class="col-md-4"><span class="text-weight-bold">Portaria</span></div>
                              <div class="col-md-4" v-if="dataset.clienteorigem.portaria_hr1_i || dataset.clienteorigem.portaria_hr1_f">{{ $helpers.ifnull(dataset.clienteorigem.portaria_hr1_i, '-') + ' à ' + $helpers.ifnull(dataset.clienteorigem.portaria_hr1_f, '-') }}</div>
                              <div class="col-md-4" v-if="dataset.clienteorigem.portaria_hr2_i || dataset.clienteorigem.portaria_hr2_f">{{ $helpers.ifnull(dataset.clienteorigem.portaria_hr2_i, '-') + ' à ' + $helpers.ifnull(dataset.clienteorigem.portaria_hr2_f, '-') }}</div>
                          </div>
                          <q-separator spaced />
                          <div class="row">
                            <div class="col-md-12">
                              <a href="javascript:void(0);" @click="$helpers.mapslink(dataset.clienteorigem.endereco.enderecocompleto)" class="text-black text-normal" title="Abrir no mapa">
                              {{dataset.clienteorigem.endereco.enderecocompletotext }}
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </q-card-section>
              </q-card>
            </q-card-section>
          </q-card>
        </div>
        <!-- cliente de origem -->

        <!-- cliente de destino -->
        <div class="col-12 q-mb-md" v-if="!loading && dataset">
          <q-card class="" bordered flat >
            <q-toolbar class="bg-grey-3 text-black" si>
              <q-avatar icon="pin_drop" text-color="black" />
              <q-toolbar-title class="text-subtitle2">
                Destinatário :: {{temclientedestino ? dataset.clientedestino.razaosocial : 'Não informado!'}}
              </q-toolbar-title>
            </q-toolbar>
            <q-card-section class="no-padding">
              <q-card flat>
                <q-card-section >
                  <div class="fit row wrap">
                    <div class="col-12">
                      <q-field label="Cliente Destinatário (Destino)" stack-label outlined :dense="!$q.platform.is.mobile" >
                        <template v-slot:control>
                          <div class="self-center full-width no-outline" tabindex="0">{{dataset.clientedestino.razaosocial}}</div>
                        </template>
                      </q-field>
                    </div>
                  </div>
                  <div class="fit row wrap q-pt-md" v-if="temclientedestino" >
                    <div class="col-md-12 col-xs-12">
                      <div class="fit row wrap">
                        <div class="col-md-12">
                          <div class="full-width row inline  justify-between items-start content-start">
                              <div class="col-md-10">CNPJ: {{$helpers.mascaraDocCPFCNPJ(dataset.clientedestino.cnpj)}}</div>
                          </div>
                          <q-separator spaced />
                          <div class="row">
                              <div class="col-md-4"><span class="text-weight-bold">Telefone</span></div>
                              <div class="col-md-4">{{$helpers.mascaratel(dataset.clientedestino.fone1) }}</div>
                              <div class="col-md-4">{{$helpers.mascaratel(dataset.clientedestino.fone2) }}</div>
                          </div>
                          <div class="row" v-if="dataset.clientedestino.temhorariosegqui()">
                              <div class="col-md-4"><span class="text-weight-bold">Seg à Qui</span></div>
                              <div class="col-md-4" v-if="dataset.clientedestino.segqui_hr1_i || dataset.clientedestino.segqui_hr1_f">{{ $helpers.ifnull(dataset.clientedestino.segqui_hr1_i, '-') + ' à ' + $helpers.ifnull(dataset.clientedestino.segqui_hr1_f, '-') }}</div>
                              <div class="col-md-4" v-if="dataset.clientedestino.segqui_hr2_i || dataset.clientedestino.segqui_hr2_f">{{ $helpers.ifnull(dataset.clientedestino.segqui_hr2_i, '-') + ' à ' + $helpers.ifnull(dataset.clientedestino.segqui_hr2_f, '-') }}</div>
                          </div>
                          <div class="row" v-if="dataset.clientedestino.temhorariosex()">
                              <div class="col-md-4"><span class="text-weight-bold">Sexta</span></div>
                              <div class="col-md-4" v-if="dataset.clientedestino.sex_hr1_i || dataset.clientedestino.sex_hr1_f">{{ $helpers.ifnull(dataset.clientedestino.sex_hr1_i, '-') + ' à ' + $helpers.ifnull(dataset.clientedestino.sex_hr1_f, '-') }}</div>
                              <div class="col-md-4" v-if="dataset.clientedestino.sex_hr2_i || dataset.clientedestino.sex_hr2_f">{{ $helpers.ifnull(dataset.clientedestino.sex_hr2_i, '-') + ' à ' + $helpers.ifnull(dataset.clientedestino.sex_hr2_f, '-') }}</div>
                          </div>
                          <div class="row" v-if="dataset.clientedestino.temhorarioportaria()">
                              <div class="col-md-4"><span class="text-weight-bold">Portaria</span></div>
                              <div class="col-md-4" v-if="dataset.clientedestino.portaria_hr1_i || dataset.clientedestino.portaria_hr1_f">{{ $helpers.ifnull(dataset.clientedestino.portaria_hr1_i, '-') + ' à ' + $helpers.ifnull(dataset.clientedestino.portaria_hr1_f, '-') }}</div>
                              <div class="col-md-4" v-if="dataset.clientedestino.portaria_hr2_i || dataset.clientedestino.portaria_hr2_f">{{ $helpers.ifnull(dataset.clientedestino.portaria_hr2_i, '-') + ' à ' + $helpers.ifnull(dataset.clientedestino.portaria_hr2_f, '-') }}</div>
                          </div>
                          <q-separator spaced />
                          <div class="row">
                            <div class="col-md-12">
                              <a href="javascript:void(0);" @click="$helpers.mapslink(dataset.clientedestino.endereco.enderecocompleto)" class="text-black text-normal" title="Abrir no mapa">
                              {{dataset.clientedestino.endereco.enderecocompletotext }}
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </q-card-section>
              </q-card>
            </q-card-section>
          </q-card>
        </div>
        <!-- cliente de destino -->

        <!-- endereço da coleta -->
        <div class="col-12 q-mb-md" v-if="!loading && dataset">
          <q-card class="" bordered flat >
            <q-toolbar class="bg-grey-3">
              <q-avatar icon="explore" />
              <q-toolbar-title class="text-subtitle2">
                Endereço da coleta ::
                <span class="text-weight-bold" v-if="(dataset.enderecocoleta.cidade ? dataset.enderecocoleta.cidade.codigo_ibge > 0 : false)">{{dataset.enderecocoleta.cidade.cidade + ' - ' + dataset.enderecocoleta.cidade.uf }}</span>
                <span v-if="!(dataset.enderecocoleta.cidade ? dataset.enderecocoleta.cidade.codigo_ibge > 0 : false)">Cidade não informada</span>
              </q-toolbar-title>
              <q-icon name="info" color="accent" size="24px" v-if="!coletanamesmacidade">
                <q-tooltip>Cidade da coleta é diferente da cidade do cliente remetente!</q-tooltip>
              </q-icon>
            </q-toolbar>
            <q-card-section class="no-padding">
              <q-card flat>
                <q-card-section>
                  <div>
                    <enderecoedit outlined label="" modeshow="uf" v-model="dataset.enderecocoleta" :dense="!$q.platform.is.mobile"
                      :maxbairro="70" :maxendereco="200" :maxnumero="10" :maxcomplemento="200" />
                  </div>
                </q-card-section>
              </q-card>
            </q-card-section>
          </q-card>
        </div>
        <!-- endereço da coleta -->

        <!-- info geral -->
        <div class="col-12 q-mb-md" v-if="!loading && dataset">
          <q-card class="" bordered flat >
            <q-toolbar class="bg-grey-3">
              <q-avatar icon="info" />
              <q-toolbar-title class="text-subtitle2">
                Informação gerais
              </q-toolbar-title>
            </q-toolbar>
            <q-card-section class="no-padding">
              <q-card flat>
                <q-card-section>
                  <div class="fit row wrap q-col-gutter-x-md q-col-gutter-y-md q-pb-md">
                    <div class="col-md-3 col-xs-12">
                      <q-field label="Data da coleta" stack-label outlined :dense="!$q.platform.is.mobile" >
                        <template v-slot:control>
                          <div class="self-center full-width no-outline" tabindex="0">{{ $helpers.dateToBR(dataset.dhcoleta) }}</div>
                        </template>
                      </q-field>
                    </div>
                    <div class="col-md-9 col-xs-12 text-right">
                      <q-icon class="q-mr-xs" name="local_shipping" color="purple" size="20px" v-if="dataset.veiculoexclusico" ><q-tooltip>Veículo exclusivo</q-tooltip></q-icon>
                      <q-icon class="q-mr-xs" name="flash_on" color="amber-14" size="20px" v-if="dataset.cargaurgente" ><q-tooltip>Carga urgente</q-tooltip></q-icon>
                      <q-icon class="q-mr-xs" name="fas fa-radiation" color="red" size="20px" v-if="dataset.produtosperigosos" ><q-tooltip>Produtos perigosos</q-tooltip></q-icon>
                    </div>
                    <div class="col-xs-12">
                      <q-field label="Motorista" stack-label outlined :dense="!$q.platform.is.mobile" >
                        <template v-slot:control>
                          <div class="self-center full-width no-outline" tabindex="0">{{ dataset.motorista ? dataset.motorista.nome : 'Não indicado' }}</div>
                        </template>
                      </q-field>
                    </div>
                    <div class="col-md-6 col-xs-12">
                      <q-field label="Nome do contato" stack-label outlined :dense="!$q.platform.is.mobile" >
                        <template v-slot:control>
                          <div class="self-center full-width no-outline" tabindex="0">{{ dataset.contatonome }}</div>
                        </template>
                      </q-field>
                    </div>
                    <div class="col-md-6 col-xs-12">
                      <q-field label="E-mail do contato" stack-label outlined :dense="!$q.platform.is.mobile" >
                        <template v-slot:control>
                          <div class="self-center full-width no-outline" tabindex="0">{{ dataset.contatoemail }}</div>
                        </template>
                      </q-field>
                    </div>
                    <div class="col-md-3 col-xs-12">
                      <q-field label="Peso" stack-label outlined :dense="!$q.platform.is.mobile" >
                        <template v-slot:control>
                          <div class="self-center full-width no-outline" tabindex="0">{{ dataset.peso }}</div>
                        </template>
                      </q-field>
                    </div>
                    <div class="col-md-3 col-xs-12">
                      <q-field label="Quantidade" stack-label outlined :dense="!$q.platform.is.mobile" >
                        <template v-slot:control>
                          <div class="self-center full-width no-outline" tabindex="0">{{ dataset.qtde ? dataset.qtde : '-' }}</div>
                        </template>
                      </q-field>
                    </div>
                    <div class="col-md-6">
                      <q-field label="Espécie" stack-label outlined :dense="!$q.platform.is.mobile" >
                        <template v-slot:control>
                          <div class="self-center full-width no-outline" tabindex="0">{{ dataset.especie }}</div>
                        </template>
                      </q-field>
                    </div>
                    <div class="col-12">
                      <q-field label="Observação" stack-label outlined :dense="!$q.platform.is.mobile" >
                        <template v-slot:control>
                          <div class="self-center full-width no-outline" tabindex="0">{{ dataset.obs }}</div>
                        </template>
                      </q-field>
                    </div>
                    <div class="col-12 text-caption text-weight-bold">
                      <q-separator  />
                      Gestão do cliente
                    </div>
                    <div class="col-md-6 col-xs-12">
                      <q-field label="Nº Ordem de compra" stack-label outlined :dense="!$q.platform.is.mobile" >
                        <template v-slot:control>
                          <div class="self-center full-width no-outline" tabindex="0">{{ dataset.gestaocliente_ordemcompra }}</div>
                        </template>
                      </q-field>
                    </div>
                    <div class="col-md-6 col-xs-12">
                      <q-field label="Comprador" stack-label outlined :dense="!$q.platform.is.mobile" >
                        <template v-slot:control>
                          <div class="self-center full-width no-outline" tabindex="0">{{ dataset.gestaocliente_comprador }}</div>
                        </template>
                      </q-field>
                    </div>
                    <div class="col-12">
                      <q-field label="Itens da compra" stack-label outlined :dense="!$q.platform.is.mobile" >
                        <template v-slot:control>
                          <div class="self-center full-width no-outline" tabindex="0">{{ dataset.gestaocliente_itenscomprador }}</div>
                        </template>
                      </q-field>
                    </div>
                  </div>
                </q-card-section>
              </q-card>
            </q-card-section>
          </q-card>
        </div>
        <!-- info geral -->

        <!-- produtos perigosos itens -->
        <div class="col-12 q-mb-md" v-if="!loading && dataset" ref="produtosperigosos">
          <q-card class="" bordered flat >
            <q-toolbar class="bg-grey-3" v-if="!dataset.produtosperigosos">
              <q-avatar icon="fas fa-radiation" />
              <q-toolbar-title class="text-subtitle2">
                <span >Sem Produtos perigosos</span>
              </q-toolbar-title>
            </q-toolbar>
            <q-toolbar class="bg-grey-3"  v-if="dataset.produtosperigosos">
              <q-avatar icon="fas fa-radiation" />
              <q-toolbar-title class="text-subtitle2">
                <span >Produtos perigosos</span>
              </q-toolbar-title>
            </q-toolbar>
            <q-card-section class="no-padding" v-if="dataset.produtosperigosos">
              <q-card flat>
                <q-card-section v-if="dataset.produtosperigosos && (dataset.itens ? dataset.itens.length === 0 : true)">
                  <div class="fit row wrap text-red">
                    <div class="col-12 text-center">Nenhum item lançado!</div>
                    <div class="col-12 text-center">Informe um produto ou desmarque a opção de produtos perigosos da coleta!</div>
                  </div>
                </q-card-section>
                <q-card-section v-if="dataset.produtosperigosos" class="no-padding">
                  <div class="row full-width" v-if="(dataset.itens ? dataset.itens.length > 0 : false)">
                    <div class="col-12" >
                      <q-table flat :data="dataset.itens" :columns="columns" separator="cell" dense :rows-per-page-options="[0]" hide-bottom :loading="loadingitens" ref="table1" >
                          <template v-slot:body-cell-produto="props">
                            <q-td :props="props" style="max-width: 300px" class="wrap">
                              <div class="fit row wrap" v-if="props.row.produto" >
                                {{ ((props.row.produto.onu ? props.row.produto.onu !== '' : false) ? 'ONU: ' + props.row.produto.onu + ' - ' : '') + props.row.produto.nome }}
                              </div>
                            </q-td>
                          </template>
                          <template v-slot:body-cell-embalagem="props">
                            <q-td :props="props" >
                              <div v-if="props.row.embalagem">
                                {{props.row.embalagem.descricao}}<q-tooltip>{{props.row.embalagem.sigla}}</q-tooltip>
                              </div>
                            </q-td>
                          </template>
                      </q-table>
                    </div>
                  </div>
                </q-card-section>
              </q-card>
            </q-card-section>
          </q-card>
        </div>
        <!-- produtos perigosos itens -->
        <q-separator spaced  v-if="mode == 'integrado'" />
        <q-card flat>
          <q-card-section>
            <div class="row" v-if="mode == 'integrado'" >
              <q-btn label="Voltar" color="primary" flat class="q-ml-sm" @click="$router.go(-1)" v-if="mode == 'integrado'" />
              <q-btn label="Fechar" color="primary" flat class="q-ml-sm" @click="actClose" v-if="mode == 'dialog'" />
            </div>
          </q-card-section>
        </q-card>
    </q-card-section>
  </q-card>
</div>
</template>

<script>
import moment from 'moment'
import Coletas from 'src/mvc/collections/coletas.js'
import Coleta from 'src/mvc/models/coleta.js'
import Embalagens from 'src/mvc/collections/embalagens.js'
import enderecoedit from 'src/components/cpn-endereco-edit-viewer.vue'
export default {
  name: 'coleta.edit.cnpj',
  components: {
    enderecoedit
  },
  props: {
    mode: {
      type: String,
      default: function () {
        return 'integrado' // ou dialog
      }
    },
    adding: {
      type: Boolean,
      default: function () {
        return true
      }
    },
    idstart: {
      type: Number,
      default: function () {
        return null
      }
    }
  },
  data () {
    var dataset = new Coleta()
    var embalagemoptions = new Embalagens()
    return {
      tevealteracao: false,
      checkingemaberto: false,
      coletasemabertoremetente: 0,
      showcartaocnpj: false,
      cnpjloading: false,
      loadingitens: false,
      showemailedit: false,
      showadd: false,
      tab: 'dados',
      editItemIdx: null,
      editItem: null,
      saving: false,
      dataset,
      showpdf: false,
      srcpdf: null,
      breadcrumbslist: [
        { to: { name: 'operacional.coletas.consulta' }, label: 'Coletas' }
      ],
      embalagemoptions,
      loading: true,
      deleting: false,
      posting: false,
      msgError: '',
      columns: [
        { name: 'produto', align: 'left', label: 'Produto', field: row => row.produto.nome },
        { name: 'qtde', label: 'Quantidade', field: 'qtde' },
        { name: 'embalagem', label: 'Embalagem', field: 'embalagem', sortable: true }
      ]
    }
  },
  async mounted () {
    var app = this
    moment.locale('pt-br')
    await app.init()
  },
  computed: {
    temclienteorigem () {
      return (this.dataset ? this.dataset.temclienteorigem() : false)
    },
    temclientedestino () {
      return (this.dataset ? this.dataset.temclientedestino() : false)
    },
    coletanamesmacidade () {
      if (this.loading) return false
      if (!this.dataset.enderecocoleta) return false
      if (!this.dataset.enderecocoleta.cidade.codigo_ibge) return false
      if (!(this.dataset.enderecocoleta.cidade.codigo_ibge > 0)) return false

      if (!this.dataset.clienteorigem) return false
      if (!(this.dataset.clienteorigem.id > 0)) return false
      if (!this.dataset.clienteorigem.endereco.cidade.codigo_ibge) return false
      if (!(this.dataset.clienteorigem.endereco.cidade.codigo_ibge > 0)) return false
      return (this.dataset.clienteorigem.endereco.cidade.codigo_ibge === this.dataset.enderecocoleta.cidade.codigo_ibge)
    }
  },
  methods: {
    onEnderecoColetaUpdated () {
      this.onChangeCidadeRegiao()
    },
    actSetFocusStart () {
      this.$refs.txtremetente.actOnFocus()
    },
    actSetFocusDestino () {
      try {
        if (!this.dataset.clientedestino) throw new Error('Nenhum produto informado')
        if (!(this.dataset.clientedestino.id > 0)) throw new Error('Nenhum produto informado')
      } catch (error) {
        this.$refs.txtdestinatario.actOnFocus()
      }
    },
    async openEmAbertos () {
      var app = this
      let props = this.$router.resolve({
        name: 'operacional.coletas.consulta',
        query: { situacao: ['0', '1'], clienteorigem: [app.dataset.clienteorigem.id] }
      })
      var link = props.href
      app.$helpers.href(link)
    },
    async checkColetasEmAbertoRemetente (pCliente) {
      var app = this
      if (app.checkingemaberto) return
      try {
        app.checkingemaberto = true
        app.coletasemabertoremetente = 0
        if (app.loading) throw new Error('Em loading')
        if (!app.dataset) throw new Error('Nenhum dataset')
        if (!app.adding) throw new Error('Não é modo de inserção')
        if (!pCliente) throw new Error('Sem cliente')
        if (!(pCliente.id > 0)) throw new Error('cliente sem id > 0')

        var coletasemaberto = new Coletas()
        coletasemaberto.situacao = ['0', '1']
        coletasemaberto.clienteorigem = [pCliente.id]
        var ret = await coletasemaberto.fetch()
        if (ret.ok) {
          app.coletasemabertoremetente = coletasemaberto.itens ? coletasemaberto.pagination.rowsNumber : 0
        } else {
          if (ret.msg ? ret.msg !== '' : false) throw new Error('Erro na consulta :: ' + ret.msg)
        }
      } catch (error) {
        // console.error(error.message)
      }
      setTimeout(() => {
        app.checkingemaberto = false
      }, 300)
    },
    async onChangeCidadeRegiao (pForce = false) {
      var app = this
      try {
        if (app.loading) throw new Error('Aguarde concluir o carregamento da página')
        if (!app.dataset) throw new Error('Nenhum registro carregado')
        if (!((app.dataset.situacao.value === '1') || (app.dataset.situacao.value === '0'))) throw new Error('Coleta bloqueada!')
        if (!(app.dataset.enderecocoleta)) throw new Error('Coleta sem endereço')
        if (!(app.dataset.enderecocoleta.cidade)) throw new Error('Coleta sem cidade')
        if (!(app.dataset.enderecocoleta.cidade.regiao)) throw new Error('Cidade da coleta sem região')
        if (!(app.dataset.enderecocoleta.cidade.regiao.id > 0)) throw new Error('Coleta sem região associada')
        if (!pForce) {
          if (!app.dataset.enderecocoleta.cidade.regiao.sugerirmotorista) throw new Error('Região não permite sugestão')
        }
        var ret = await app.dataset.enderecocoleta.cidade.regiao.dialogUltimosMotoristaPorColeta(app, app.dataset.clienteorigem.id)
        if (ret.ok) {
          if (ret.motorista ? ret.motorista.id > 0 : false) {
            await app.dataset.motorista.cloneFrom(ret.motorista)
            app.$q.notify({
              caption: 'Motorista atualizado!',
              message: 'Novo motorista: ' + app.dataset.motorista.nome,
              color: 'positive',
              actions: [
                { label: 'OK', color: 'white', handler: () => { /* ... */ } }
              ]
            })
          }
        }
      } catch (error) {
        if (pForce) {
          app.$q.notify({
            message: 'Sugestão de motorista',
            caption: error.message,
            position: 'center',
            color: 'negative',
            actions: [
              { label: 'OK', color: 'white', handler: () => { /* ... */ } }
            ]
          })
        }
        return false
      }
    },
    async actCopyEndereco (pButtonIndex) {
      var app = this
      if (!app.dataset) return
      if (pButtonIndex === 0) {
        app.dataset.enderecocoleta.cloneFrom(app.dataset.clienteorigem.endereco)
      } else {
        app.dataset.enderecocoleta.cloneFrom(app.dataset.clientedestino.endereco)
      }
      app.onChangeCidadeRegiao()
      app.$refs.txtenderecocoleta.refresh()
    },
    async init () {
      var app = this
      // app.adding = (this.$route.name === 'operacional.coletas.coleta.add')
      if (app.adding) {
        app.idstart = null
        app.loading = true
        app.dataset.limpardados()
        app.loading = false
        app.$nextTick(() => {
          // app.$refs.txtselect.$el.focus()
          app.actSetFocusStart()
        })
      } else {
        app.loading = true
        // if (app.$route.params.id) {
        // app.idstart = app.$route.params.id
        await app.refreshData()
        // }
        if (app.$route.query.tab) {
          app.tab = app.$route.query.tab
        }
      }
    },
    scrollToElement (refName) {
      var el = this.$refs[refName]
      window.scrollTo(0, el.offsetTop)
    },
    actClose () {
      this.$emit('close', this.tevealteracao)
    },
    async refreshData () {
      var app = this
      app.loading = true
      app.msgError = ''
      var ret = await app.dataset.find(app.idstart)
      if (ret.ok) {
        if (app.dataset.itens) {
          if (app.dataset.itens.length > 0) app.editItem = app.dataset.itens[0]
        }
      } else {
        app.loading = false
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {
        })
      }
      app.loading = false
    }
  }
}
</script>
<style scoped>
.q-table--no-wrap th, .q-table--no-wrap td {
    /* don't shorten cell contents */
    white-space: normal !important;
    word-wrap: break-word;
}
.containerpdf {
  max-width: 100%;
  max-height: 70%;
  min-width: 1000px;
  min-height: 600px;
  width: 100%;
  height: 100%;
}
</style>
