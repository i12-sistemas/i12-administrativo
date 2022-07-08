<template>
<div>
  <q-page class="">
    <div class="row doc-header" :class="$q.platform.is.mobile ? '' : 'q-pa-lg'">
      <div class="col-sm-12" >
        <div class="col-12" v-if="error">
          <q-banner class="bg-negative text-white">{{error}}</q-banner>
        </div>
        <q-card class="bg-white" flat :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile">
          <q-card-section>
            <div class="row q-col-gutter-md">
              <div class="col-sm-12 col-md-6">
                <div class="col-12 ">
                  <div class="ellipsis">Informe qualquer Informação para pesquisar</div>
                  <div class="text-caption  text-grey-7 ellipsis">Ex.: CNPJ, número, remetente, destinatário, coleta...</div>
                </div>
                <div class="col-sm-12 col-md-8 col-lg-6">
                  <q-input outlined debounce="700" v-model="filter" placeholder="Procurar" label="Procurar" clearable>
                    <template v-slot:prepend>
                      <q-icon name="search" />
                    </template>
                  </q-input>
                </div>
              </div>
              <div class="col-sm-12 col-md-6">
                <div class="col-12 ">
                  <div class="ellipsis">Você também pode restrigir a consulta pela data de coleta</div>
                  <div class="text-caption text-grey-7 ellipsis">Essa opção é opcional</div>
                </div>
                <div class="col-sm-12 col-md-8 col-lg-6">
                  <q-select v-model="customfilter" :options="optionsfilter" label="Filtro" outlined emit-value stack-label map-options class="q-mr-xs" >
                    <template v-slot:selected-item="scope">
                      <div class="ellipsis q-pt-xs" >
                        <q-icon :name="scope.opt.icon" size="16px" :color="scope.opt.color" class="q-mr-sm" />
                        <span>{{ scope.opt.desc }}</span>
                        <q-tooltip :delay="700">
                          <div>{{ scope.opt.tooltip }}</div>
                        </q-tooltip>
                      </div>
                  </template>
                    <template v-slot:option="scope">
                      <q-item v-bind="scope.itemProps" v-on="scope.itemEvents"  >
                        <q-item-section avatar>
                          <q-icon :name="scope.opt.icon"  :color="scope.opt.color" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>{{ scope.opt.desc }}</q-item-label>
                          <q-item-label caption>{{ scope.opt.tooltip }}</q-item-label>
                        </q-item-section>
                      </q-item>
                    </template>
                  </q-select>
                </div>
              </div>
            </div>
          </q-card-section>
          <q-card-section>
            <div class="row">
            <q-btn unelevated label="Consultar" color="primary" icon="search"  @click="refreshData(null)" :loading="loading"  />
            </div>
          </q-card-section>
        </q-card>
        <q-card class="bg-white q-mt-md" flat  v-if="rows ? rows.length > 0 : false" :bordered="!$q.platform.is.mobile" :square="$q.platform.is.mobile">
          <q-separator v-if="$q.platform.is.mobile"  />
          <q-card-section class="q-pa-none">
            <div style="max-width: 100vw">
              <q-table :data="rows" :columns="columns" :dense="!$q.platform.is.mobile"  flat
                :loading="loading" color="primary" id="sticky-table"
                :pagination.sync="dataset.pagination"
                row-key="id"
                :rows-per-page-options="[15]"
                @request="refreshData"
                :filter="filter"
                >
                  <!-- headers -->
                  <template v-slot:header-cell-clienteorigem="props">
                    <q-th :props="props" >
                      <div>
                        <q-icon name="trip_origin" text-color="black"/><span class="q-ml-xs">Origem</span>
                        <q-icon name="pin_drop" text-color="black" class="q-ml-md"/><span class="q-ml-xs">Destino</span>
                        <q-tooltip :delay="700">
                            <div><q-icon name="trip_origin" text-color="black"/> Remetente</div>
                            <div><q-icon name="pin_drop" text-color="black"/> Destinatário</div>
                        </q-tooltip>
                      </div>
                    </q-th>
                  </template>
                  <!-- headers -->
                  <template v-slot:body-cell-action="props">
                    <q-td :props="props">
                      <q-btn flat round :dense="!$q.platform.is.mobile" :size="$q.platform.is.mobile ? '12px' : '8px'" icon="edit" @click="actEditDialog(props.pageIndex)" >
                        <q-tooltip>Abrir coleta</q-tooltip>
                      </q-btn>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-id="props">
                    <q-td :props="props" >
                      <div @click="actEditDialog(props.pageIndex)" class="cursor-pointer" >
                        {{props.row.id}}
                        <q-tooltip>
                          <div>Coleta # {{props.row.id}}</div>
                          <div>Origem: {{props.row.origem.description}}</div>
                        </q-tooltip>
                      </div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-nota="props">
                    <q-td :props="props" >
                      <div @click="actRastrear(props.row)" class="cursor-pointer" >
                        <div v-if="props.row.notascount > 0">
                          <div v-if="props.row.notascount === 1"> {{props.row.notas[0].notanumero}}</div>
                          <div v-if="props.row.notascount > 1">* {{props.row.notascount}} nota(s)</div>
                          <q-tooltip>
                            <div>Notas coletadas</div>
                            <div v-for="(notas, notaidx) in props.row.notas" :key="notaidx" class="q-mt-sm">
                              <div>Número: {{notas.notanumero}} - Série: {{notas.notaserie}}</div>
                              <div v-if="notas.notadh">Emissão: {{$helpers.dateToBR(notas.notadh)}}</div>
                              <div>{{notas.remetentenome}}</div>
                            </div>
                          </q-tooltip>
                        </div>
                        <div v-else>
                          <div v-if="props.row.nfe ? props.row.nfe.nNF > 0 : false">
                            {{props.row.nfe.nNF }}
                          </div>
                          <q-tooltip>
                            <div>Coleta # {{props.row.id}}</div>
                            <div>Origem: {{props.row.origem.description}}</div>
                            <div v-if="props.row.created_at">Inserida:  {{ $helpers.datetimeToBR(props.row.created_at, false, true) }} por {{ props.row.created_usuario ? props.row.created_usuario.nome : '** Não identificado **' }}</div>
                            <div v-if="(props.row.updated_at !== props.row.created_at)">Alterada:  {{ $helpers.datetimeToBR(props.row.updated_at, false, true) }} por {{ props.row.updated_usuario ? props.row.updated_usuario.nome : '** Não identificado **' }}</div>
                            <div v-if="props.row.notascount > 0">
                              <div v-if="props.row.notascount === 1">Existe 1 nota fiscal associada</div>
                              <div v-if="props.row.notascount > 1">Existem {{props.row.notascount}} nota(s) fiscais</div>
                              <div v-for="(notas, notaidx) in props.row.notas" :key="notaidx">
                                <div>Número: {{notas.notanumero}}<span v-if="notas.notadh"> - Emissão: {{$helpers.dateToBR(notas.notadh)}}</span> - {{notas.remetentenome}}</div>
                              </div>
                            </div>
                          </q-tooltip>
                        </div>
                      </div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-situacao="props">
                    <q-td :props="props" >
                      <!-- <div style="width: 70px" class="text-center" v-if="props.row.statuscliente.value > 0" >
                        <q-icon size="24px" :color="props.row.statuscliente.color" :name="props.row.statuscliente.icon" />
                        {{props.row.statuscliente.description}}
                        <q-tooltip :delay="500" >
                          <div>{{props.row.statuscliente.description}}</div>
                          <div>{{props.row.statuscliente.tooltip}}</div>
                        </q-tooltip>
                      </div> -->
                      <div class="ellipsis text-white rounded-borders q-px-xs text-weight-bold"  v-if="props.row.statuscliente.value > 0" :class="'bg-' + props.row.statuscliente.color">
                        {{props.row.statuscliente.description}}
                        <q-tooltip :delay="500">
                          <div>Status: {{props.row.statuscliente.description}}</div>
                          <div>{{props.row.statuscliente.tooltip}}</div>
                        </q-tooltip>
                      </div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-dhcoleta="props">
                    <q-td :props="props" >
                      <div class="cursor-pointer" @click="actEditDialog(props.pageIndex)" >
                        {{ $helpers.dateToBR(props.row.dhcoleta) }}
                        <q-tooltip>
                          <div class="q-mr-xs" v-if="$helpers.diffDate (props.row.dhcoleta, '', 'days') > 1 && ((props.row.situacao.value == '0') || (props.row.situacao.value == '1'))" >Atrasado</div>
                          <div>{{ $helpers.datetimeRelativeToday(props.row.dhcoleta) }}</div>
                        </q-tooltip>
                      </div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-dhbaixa="props">
                    <q-td :props="props" >
                      <div  style="max-width: 150px" v-if="props.row.dhbaixa" class="cursor-pointer" @click="actEditDialog(props.pageIndex)" >
                        <span class="q-mr-xs">{{ $helpers.dateToBR(props.row.dhbaixa) }}</span>
                        <q-tooltip>
                          <div>{{ $helpers.datetimeRelativeToday(props.row.dhbaixa) }}</div>
                          <div>em {{ $helpers.datetimeToBR(props.row.dhbaixa) }}</div>
                          <div>Tempo de encerramento: {{$helpers.diffDate(props.row.dhcoleta, props.row.dhbaixa, 'days') }} dias</div>
                        </q-tooltip>
                      </div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-clienteorigem="props">
                    <q-td :props="props" >
                      <div v-if="props.row.clienteorigem ? props.row.clienteorigem.cnpj !== '' : false" class="cursor-pointer" style="min-width: 150px" @click="actEditDialog(props.pageIndex)"
                        :class="(usuario ? (usuario.clientescnpj.indexOf(props.row.clienteorigem.cnpj) >= 0) : false) ? 'text-grey-6' : 'text-black text-weight-medium'"
                      >
                        <q-icon name="trip_origin" class="q-mr-sm" :color="(usuario ? (usuario.clientescnpj.indexOf(props.row.clienteorigem.cnpj) >= 0) : false) ? 'grey-6' : 'black'"/>
                        <span>{{ props.row.clienteorigem.razaosocial }}</span>
                        <q-tooltip :delay="700">
                          <div>Remetente</div>
                          <div class="text-weight-bold">({{props.row.clienteorigem.razaosocial}})</div>
                          <div v-if="props.row.clienteorigem.fantasia != ''" >Fantasia: {{ props.row.clienteorigem.fantasia }}</div>
                          <div v-if="props.row.clienteorigem.cnpj != ''" >CNPJ: {{ $helpers.mascaraDocCPFCNPJ(props.row.clienteorigem.cnpj) }}</div>
                        </q-tooltip>
                      </div>
                      <div v-if="props.row.clientedestino ? props.row.clientedestino.cnpj !== '' : false" class="cursor-pointer"
                        :class="(usuario ? (usuario.clientescnpj.indexOf(props.row.clientedestino.cnpj) >= 0) : false) ? 'text-grey-6' : 'text-black text-weight-medium'"
                          style="min-width: 150px" @click="actEditDialog(props.pageIndex)">
                        <q-icon name="pin_drop" class="q-mr-sm" :color="(usuario ? (usuario.clientescnpj.indexOf(props.row.clientedestino.cnpj) >= 0) : false) ? 'grey-6' : 'black'"/>
                        <span>{{ props.row.clientedestino.razaosocial }}</span>
                        <q-tooltip :delay="700">
                          <div>Destinatário</div>
                          <div class="text-weight-bold">({{props.row.clientedestino.razaosocial}})</div>
                          <div v-if="props.row.clientedestino.fantasia != ''" >Fantasia: {{ props.row.clientedestino.fantasia }}</div>
                          <div v-if="props.row.clientedestino.cnpj != ''" >CNPJ: {{ $helpers.mascaraDocCPFCNPJ(props.row.clientedestino.cnpj) }}</div>
                        </q-tooltip>
                      </div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-enderecocoleta="props">
                    <q-td :props="props">
                      <div v-if="props.row.enderecocoleta ? props.row.enderecocoleta.cidade.id > 0 : false" class="cursor-pointer ellipsis" style="min-width: 100px" @click="actEditDialog(props.pageIndex)">
                        <div v-if="props.row.regiaocoleta ? props.row.regiaocoleta.id > 0 : false" class="text-caption"></div>
                        <div>{{ props.row.enderecocoleta.cidade.cidade + '/' + props.row.enderecocoleta.cidade.uf }}</div>
                        <q-tooltip :delay="200">
                          <div class="text-caption text-weight-bold">Endereço da coleta</div>
                          <div v-if="props.row.enderecocoleta ? props.row.enderecocoleta.cidade.id > 0 : false" >{{ props.row.enderecocoleta.enderecocompletotext }}</div>
                          <div v-if="props.row.regiaocoleta ? props.row.regiaocoleta.id > 0 : false">Região: {{ props.row.regiaocoleta.id + ' - '+ props.row.regiaocoleta.regiao }}</div>
                          <div class="q-mt-md text-caption text-weight-bold">Telefones cliente de origem</div>
                          <div v-if="props.row.clienteorigem.fone1 ? props.row.clienteorigem.fone1 != '' : false" >{{ $helpers.mascaratel(props.row.clienteorigem.fone1) }}</div>
                          <div v-if="props.row.clienteorigem.fone2 ? props.row.clienteorigem.fone2 != '' : false" >{{ $helpers.mascaratel(props.row.clienteorigem.fone2) }}</div>
                        </q-tooltip>
                      </div>
                    </q-td>
                  </template>
                  <template v-slot:body-cell-cidadedestino="props">
                    <q-td :props="props" >
                      <div v-if="props.row.clientedestino ? props.row.clientedestino.id > 0 : false" class="cursor-pointer ellipsis" style="min-width: 100px" @click="actEditDialog(props.pageIndex)">
                        <div v-if="props.row.clientedestino.endereco ? props.row.clientedestino.endereco.cidade.id > 0 : false" class="text-caption" >
                          {{ props.row.clientedestino.endereco.cidade.cidade + '/' + props.row.clientedestino.endereco.cidade.uf }}
                        </div>
                        <q-tooltip :delay="700">
                          <div class="text-caption text-weight-bold">Endereço da coleta</div>
                          <div v-if="props.row.enderecocoleta ? props.row.enderecocoleta.cidade.id > 0 : false" >{{ props.row.enderecocoleta.enderecocompletotext }}</div>
                          <div v-if="props.row.regiaocoleta ? props.row.regiaocoleta.id > 0 : false">Região: {{ props.row.regiaocoleta.id + ' - '+ props.row.regiaocoleta.regiao }}</div>
                        </q-tooltip>
                      </div>
                      <div v-if="props.row.clientedestino ? !(props.row.clientedestino.id > 0) : true" class="text-red">
                        Indefinido
                        <q-tooltip :delay="700">Nenhum cliente definido</q-tooltip>
                      </div>
                    </q-td>
                  </template>
              </q-table>
            </div>
          </q-card-section>
        </q-card>
        <q-page-sticky position="top" expand>
          <q-toolbar class="bg-white text-primary shadow-up-21">
            <q-btn flat dense round @click="$store.dispatch('homedashboard/togglemenu')" aria-label="Menu" icon="menu" />
            <q-btn flat round icon="arrow_back" @click="$router.go(-1)"/>
            <q-separator vertical inset v-if="!$q.platform.is.mobile" spaced/>
            <q-toolbar-title>Coletas</q-toolbar-title>
            <q-btn  icon="sync" :label="$q.platform.is.mobile ? '' : 'Atualizar'" :round="$q.platform.is.mobile" flat @click="refreshData(null)" :loading="loading" />
          </q-toolbar>
        </q-page-sticky>
      </div>
    </div>
    <!-- desktop mode -->
  </q-page>
</div>
</template>

<script>
import Coletas from 'src/mvc/collections/coletas.js'
import { ColetaSituacaoCliente } from 'src/mvc/models/enums/coletatypes'
export default {
  name: 'coletas.consulta',
  components: {
  },
  data () {
    var dataset = new Coletas()
    return {
      dataset,
      rows: [],
      customfilter: null,
      filter: '',
      optionsfilter: [],
      columns: [
        { name: 'action', align: 'left', label: '*', field: 'id' },
        { name: 'id', align: 'center', label: 'Nº Coleta', field: 'id', filterconfig: { value: null } },
        { name: 'nota', align: 'center', label: 'Nº NF-e', field: 'nota', filterconfig: { value: null } },
        { name: 'situacao', align: 'left', style: 'min-width: 120px', label: 'Situação', field: 'situacao', filterconfig: { tipo: 'tablefiltersituacao', value: ['0', '1'], label: '', info: '' } },
        { name: 'dhcoleta', style: 'width: 70px', align: 'left', label: 'Agendado', field: 'dhcoleta' },
        { name: 'dhbaixa', align: 'left', label: 'Coletado em', field: 'dhbaixa' },
        { name: 'clienteorigem', align: 'left', label: 'Cliente de origem', field: row => row.clienteorigem.razaosocial },
        { name: 'enderecocoleta', align: 'left', label: 'Cidade da coleta', field: row => row.enderecocoleta.cidade.cidade },
        { name: 'cidadedestino', align: 'left', label: 'Cidade destino', field: row => row.clientedestino.cidade }
      ],
      loading: false,
      posting: false,
      error: null,
      usuario: null,
      customcnpj: null,
      customnumero: null,
      customchave: null
    }
  },
  created: function () {
    this.$eventbus.$on('coletasconsultaupdated', this.coletasconsultaupdated)
    // this.$eventbus.$emit('setdrawerr', 'drcoletasdashboad', false)
  },
  beforeDestroy: function () {
    this.$eventbus.$off('coletasconsultaupdated', this.coletasconsultaupdated)
  },
  async mounted () {
    var app = this
    var o = new ColetaSituacaoCliente('1')

    this.optionsfilter = []
    for (let index = 0; index < o.raw.length; index++) {
      this.optionsfilter.push(o.raw[index])
    }
    this.optionsfilter.push({
      value: null,
      desc: 'Todos os registros',
      title: 'Todos os registros',
      tooltip: 'Remover todos os filtros e ver todos os registros',
      icon: 'done_all',
      color: 'grey-9',
      textcolor: 'white'
    })
    app.usuario = null
    if (app.$store.state.usuario.logado) {
      if (app.$store.state.usuario.user) {
        app.usuario = app.$store.state.usuario.user
      }
    }
    if (app.$route.query) app.queryRead(app.$route.query)
    await app.refreshData(null)
  },

  methods: {
    async actRastrear (pRow) {
      var chave = null
      var cnpj = null

      if (pRow.notascount ? pRow.notascount >= 1 : false) {
        chave = pRow.notas[0].chave
      } else {
        if (pRow.nfe ? pRow.nfe.nNF > 0 : false) {
          chave = pRow.nfe.chave
        }
      }

      if (pRow.clientedestino ? pRow.clientedestino.cnpj !== '' : false) {
        cnpj = pRow.clientedestino.cnpj
      }
      if ((chave ? chave !== '' : false) && (cnpj ? cnpj !== '' : false)) {
        this.$router.push({ name: 'rastrear.entrega', query: { chave: chave, cnpj: cnpj } })
      }
    },
    async coletasconsultaupdated (status) {
      var app = this
      app.customfilter = parseInt(status)
      await this.refreshData(null)
    },
    async queryRead (pQuery) {
      var app = this
      if (pQuery) {
        if (pQuery.page) app.dataset.pagination.page = parseInt(pQuery.page)
        if (pQuery.customfilter) {
          app.customfilter = parseInt(pQuery.customfilter)
          if (!(app.customfilter > 0)) app.customfilter = null
        }

        if (pQuery.customcnpj) app.customcnpj = pQuery.customcnpj
        if (pQuery.customnumero) app.customnumero = pQuery.customnumero
        if (pQuery.customchave) app.customchave = pQuery.customchave

        for (let index = 0; index < app.columns.length; index++) {
          const element = app.columns[index]
          if (element.filterconfig) {
            if ((element.name === 'id') && (pQuery.numero)) {
              element.filterconfig.value = app.$route.query.numero
            }

            if (element.name === 'situacao' && pQuery.situacao) {
              element.filterconfig.value = pQuery.situacao.toString().split(',')
            }
            if (element.name === 'clientedestino' && pQuery.clientedestino) {
              element.filterconfig.value = pQuery.clientedestino.toString().split(',')
            }
            if (element.name === 'clienteorigem' && pQuery.clienteorigem) {
              element.filterconfig.value = pQuery.clienteorigem.toString().split(',')
            }

            if (element.name === 'clienteorigem' && pQuery.clienteorigemstr) element.filterconfig.value2 = pQuery.clienteorigemstr
            if (element.name === 'regiao' && pQuery.regiaostr) element.filterconfig.value2 = pQuery.regiaostr
            if (element.name === 'enderecocoleta' && pQuery.enderecocoletastr) element.filterconfig.value2 = pQuery.enderecocoletastr
            if (element.name === 'clientedestino' && pQuery.clientedestinostr) element.filterconfig.value2 = pQuery.clientedestinostr
            if (element.name === 'cidadedestino' && pQuery.cidadedestinostr) element.filterconfig.value2 = pQuery.cidadedestinostr

            if (element.name === 'regiao' && pQuery.regiao) {
              element.filterconfig.value = pQuery.regiao.toString().split(',')
            }
            if (element.name === 'enderecocoleta' && pQuery.cidades) {
              element.filterconfig.value = pQuery.cidades.toString().split(',')
            }
            if (element.name === 'cidadedestino' && pQuery.cidadedestino) {
              element.filterconfig.value = pQuery.cidadedestino.toString().split(',')
            }
            if (element.name === 'origem' && pQuery.origem) {
              element.filterconfig.value = pQuery.origem.toString().split(',')
            }
            if (element.name === 'dhcoleta') {
              if (pQuery.dhcoletai || pQuery.dhcoletaf) {
                element.filterconfig.value = { dti: pQuery.dhcoletai, dtf: pQuery.dhcoletaf }
              } else {
                element.filterconfig.value = null
              }
            }

            if (element.name === 'motorista') {
              if ((pQuery.semmotorista === 'S') || (pQuery.motoristas)) {
                element.filterconfig.value = {
                  ids: pQuery.motoristas ? pQuery.motoristas.toString().split(',') : null,
                  semmotorista: (pQuery.semmotorista ? pQuery.semmotorista : null)
                }
              }
            }

            if (element.name === 'peso') {
              if (pQuery.pesoi || pQuery.pesof) {
                element.filterconfig.value = { valuei: pQuery.pesoi, valuef: pQuery.pesof }
              } else {
                element.filterconfig.value = null
              }
            }

            if (element.name === 'dhbaixa') {
              if (pQuery.dhbaixai || pQuery.dhbaixaf) {
                element.filterconfig.value = { dti: pQuery.dhbaixai, dtf: pQuery.dhbaixaf }
              } else {
                element.filterconfig.value = null
              }
            }
            if (element.name === 'categ') {
              element.filterconfig.value = []
              if (pQuery.produtosperigosos) {
                element.filterconfig.value.push({ field: 'produtosperigosos', desc: 'Produtos perigosos', value: (pQuery.produtosperigosos === '1' ? 'S' : 'N'), sim: 'Com produtos perigosos', nao: 'Sem produtos perigosos' })
              }
              if (pQuery.veiculoexclusico) {
                element.filterconfig.value.push({ field: 'veiculoexclusico', desc: 'Veículo exclusivo', value: (pQuery.veiculoexclusico === '1' ? 'S' : 'N'), sim: 'Com veículo exclusivo', nao: 'Sem veículo exclusivo' })
              }
              if (pQuery.cargaurgente) {
                element.filterconfig.value.push({ field: 'cargaurgente', desc: 'Carga urgente', value: (pQuery.cargaurgente === '1' ? 'S' : 'N'), sim: 'Com carga urgente', nao: 'Sem carga urgente' })
              }
            }
          }
        }
      }
    },
    async actEditDialog (rowIndex) {
      var app = this
      var row = app.rows[rowIndex]
      var ret = await row.dialogAddOrEdit(app)
      if (ret.ok) {
        app.refreshData(null)
      } else {
        if (ret.msg ? ret.msg !== '' : false) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {
          })
        }
      }
    },
    async actLoadMore () {
      var app = this
      app.loading = true
      var ret = await app.dataset.loadmore()
      if (ret.ok) {
        app.rows = app.dataset.itens
      }
      app.loading = false
    },
    async refreshData (props) {
      var app = this
      try {
        app.loading = true
        app.error = null
        if (!app.usuario) throw new Error('Nenhum usuário logado')
        if (!app.usuario.permitecoletasver) throw new Error('Sem permissão de acesso')

        app.error = null
        app.dataset.readPropsTable(props)
        app.dataset.customfilter = app.customfilter

        delete app.dataset.customcnpj
        if ((typeof app.customcnpj !== 'undefined') && (app.customcnpj ? app.customcnpj !== '' : false)) app.dataset.customcnpj = app.customcnpj
        app.dataset.customnumero = app.customnumero
        app.dataset.customchave = app.customchave

        app.dataset.orderby = null
        if (app.orderbylist) {
          var c = Object.keys(app.orderbylist).length
          if (c > 0) app.dataset.orderby = app.orderbylist
        }

        var ret = await app.dataset.fetch()
        if (ret.ok) {
          app.rows = app.dataset.itens
          // atualiza url
          // if (app.$route.query.t) delete app.$route.query.t
          var query = {}
          if (app.dataset.filter !== null && app.dataset.filter !== '') query.find = app.dataset.filter
          if (app.dataset.customfilter !== null) query.customfilter = app.dataset.customfilter

          if (app.dataset.customcnpj ? app.dataset.customcnpj !== '' : false) query.customcnpj = app.dataset.customcnpj
          if (app.dataset.customnumero !== null) query.customnumero = app.dataset.customnumero
          if (app.dataset.customchave !== null) query.customchave = app.dataset.customchave

          // if (app.dataset.pesoi !== null) query.pesoi = app.dataset.pesoi
          // if (app.dataset.pesof !== null) query.pesof = app.dataset.pesof
          if (app.dataset.pagination.page !== null && app.dataset.pagination.page > 1) query.page = app.dataset.pagination.page

          query.t = new Date().getTime()
          try {
            app.$router.replace({ name: app.$route.name, query: query, replace: true, append: false })
          } catch (error) {
            console.error(error)
          }
        } else {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {
          })
        }
      } catch (error) {
        app.loading = false
        app.error = error.message
      }
      app.loading = false
    }
  }
}
</script>
