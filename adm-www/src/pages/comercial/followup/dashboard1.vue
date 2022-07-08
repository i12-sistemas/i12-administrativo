<template>
<div>
  <q-page class="">
    <!-- desktop mode -->
    <div class="row doc-header" :class="$q.platform.is.mobile ? '' : 'q-pa-lg'">
      <div class="col-12" v-if="error">
        <q-banner class="bg-negative text-white">{{error}}</q-banner>
      </div>
      <div class="col-sm-12" v-if="rows && !error">
        <q-card class="my-card" flat bordered>
          <q-card-section>
            <q-item>
              <q-item-section>
                <q-item-label class="text-h6 text-weight-bold">
                  Referência: <span class="text-weight-bold">{{(rows ? (rows.periodo ? rows.periodo.dti : false) : false) ?  $helpers.dateToBR(rows.periodo.dti) : '?'}}</span>
                  <q-tooltip :delay="500" v-if="!loading">
                    Última importação de dados em <span class="text-weight-bold">{{$helpers.dateToBR(rows.periodo.dti)}}</span>
                  </q-tooltip>
                </q-item-label>
                <q-item-label class="text-body" v-if="(usuario ? usuario.followupcount >= 1 && usuario.followupcount < 3 : false) ">
                  <div v-for="(cli, k) in usuario.followupclientes" :key="'cli' + k">
                    {{$helpers.mascaraDocCPFCNPJ(cli.cnpj)}} - {{cli.fantasia}}
                  </div>
                </q-item-label>
                <q-item-label class="text-body" v-if="(usuario ? usuario.followupcount >= 2 : false) ">
                  {{usuario.followupcount}} empresas
                  <q-tooltip :delay="500">
                    <div v-for="(cli, k) in usuario.followupclientes" :key="'cli' + k">
                      {{$helpers.mascaraDocCPFCNPJ(cli.cnpj)}} - {{cli.fantasia_followup}} :: <b>{{cli.fantasia}}</b>
                    </div>
                  </q-tooltip>
                </q-item-label>
                <q-item-label class="text-body" v-if="(usuario ? usuario.followupcount === 0 : true) ">Nenhuma empresa associada no Follow Up</q-item-label>
              </q-item-section>
              <q-item-section side top>
                <q-btn  icon="filter_alt" label="Filtrar" flat @click="actNewDialog()" :loading="loading" />
              </q-item-section>
              <q-item-section side top>
                <q-btn  icon="sync" label="Atualizar" flat @click="refreshData" :loading="loading" />
              </q-item-section>
            </q-item>
          </q-card-section>
          <q-card-section>
            <div class="fit row wrap full-width q-col-gutter-md full-height">

              <div class="col-xs-12 col-sm-6 col-md-3">
                <q-card class="text-grey-9 full-width bg-white" flat bordered>
                  <q-item class="">
                    <q-item-section avatar>
                      <q-avatar size="70px" font-size="40px" text-color="blue" icon="fas fa-file-invoice-dollar" />
                    </q-item-section>
                    <q-space />
                    <q-item-section >
                        <div class="fit row wrap justify-end  full-width text-right">
                          <div class="col-12 text-subtitle2 text-grey-7">O.C. em aberto</div>
                          <div class="col-12 text-h4 text-weight-bold" v-if="loading"><q-spinner color="indigo" /></div>
                          <div class="col-12 text-h4 text-weight-bold" v-if="!loading">{{$helpers.formatRS(totalizador.ocemaberto, false, 0)}}</div>
                        </div>
                    </q-item-section>
                  </q-item>
                </q-card>

                <q-card class="text-grey-9 full-width bg-white q-mt-md " flat bordered >
                  <q-item>
                    <q-item-section avatar>
                      <q-avatar size="70px" font-size="40px" text-color="green" icon="fas fa-comment-dollar" />
                    </q-item-section>
                    <q-space />
                    <q-item-section >
                        <div class="fit row wrap justify-end  full-width text-right">
                          <div class="col-12 text-subtitle2 text-grey-7">Saldo em aberto</div>
                          <div class="col-12 text-h4 text-weight-bold" v-if="loading"><q-spinner color="indigo" /></div>
                          <div class="col-12 text-h6 text-weight-bold" v-if="!loading">{{$helpers.formatBigValue(totalizador.saldoaberto, 3)}}</div>
                          <q-tooltip :delay="700" v-if="!loading">
                            <div class="col-12 text-subtitle2">Saldo em aberto</div>
                            <div class="col-12 text-h6 text-weight-bold">{{$helpers.formatRS(totalizador.saldoaberto, true, )}}</div>
                          </q-tooltip>
                        </div>
                    </q-item-section>
                  </q-item>
                  <q-separator spaced inset  />
                  <q-item>
                    <q-item-section >
                      <div class="text-subtitle2 text-grey-7 ellipsis">Saldo vencido em</div>
                      <div class="text-subtitle2 text-grey-7 ellipsis">relação à promessa</div>
                    </q-item-section >
                    <q-item-section avatar>
                     <q-circular-progress show-value font-size="16px"
                          :value="parseInt(loading ? 0 : totalizador.saldoabertoindicador)"
                          :indeterminate="loading"
                          size="70px"
                          :thickness="0.12"
                          :color="getColorIndicador(loading ? null : totalizador.saldoabertoindicador)"
                          track-color="grey-1"
                          class="q-ma-none"
                          :class="'text-' + getColorIndicador(loading ? null : totalizador.saldoabertoindicador)"
                        >
                          {{loading ? '?' : $helpers.formatRS(totalizador.saldoabertoindicador, false, 2) + '%'}}
                        </q-circular-progress>
                    </q-item-section>
                  </q-item>
                </q-card>
              </div>

              <div class="col-xs-12 col-sm-6 col-md-3 ">
                <q-card class="text-grey-9 full-width bg-white" flat bordered>
                  <q-item>
                    <q-item-section avatar>
                      <q-avatar size="70px" font-size="40px" text-color="green" icon="fas fa-list-ul" />
                    </q-item-section>
                    <q-space />
                    <q-item-section >
                        <div class="fit row wrap justify-end text-right">
                          <div class="col-12 text-subtitle2 text-grey-7">Linhas em aberto</div>
                          <div class="col-12 text-h4 text-weight-bold" v-if="loading"><q-spinner color="indigo" /></div>
                          <div class="col-12 text-h4 text-weight-bold" v-if="!loading">{{$helpers.formatRS(totalizador.linhaemaberto, false, 0)}}</div>
                        </div>
                    </q-item-section>
                  </q-item>
                </q-card>

                <q-card class="text-grey-9 full-width bg-white q-mt-md" flat bordered >
                  <q-item>
                    <q-item-section avatar>
                      <q-avatar size="70px" font-size="40px" :text-color="getColorIndicador(loading ? null : totalizador.linhaemabertoindicador)" icon="fas fa-history" />
                    </q-item-section>
                    <q-space />
                    <q-item-section >
                        <div class="fit row wrap justify-end  full-width text-right">
                          <div class="col-12 text-subtitle2 text-grey-7 ellipsis">Linhas em aberto e vencidas</div>
                          <div class="col-12 text-h4 text-weight-bold" v-if="loading"><q-spinner color="indigo" /></div>
                          <div class="col-12 text-h4 text-weight-bold" v-if="!loading">{{$helpers.formatRS(totalizador.linhaemabertovencidas, false, 0)}}</div>
                        </div>
                    </q-item-section>
                  </q-item>
                  <q-separator spaced inset  />
                  <q-item>
                    <q-item-section >
                        <div class="text-subtitle2 text-grey-7 ellipsis">Linhas em abertas vencida</div>
                        <div class="text-subtitle2 text-grey-7 ellipsis">em relação à promessa</div>
                    </q-item-section >
                    <q-space />
                    <q-item-section avatar>
                     <q-circular-progress show-value font-size="16px"
                          :value="loading ? 0 : parseInt(totalizador.linhaemabertoindicador)"
                          :indeterminate="loading"
                          size="70px"
                          :thickness="0.12"
                          :color="getColorIndicador(loading ? null : totalizador.linhaemabertoindicador)"
                          track-color="grey-1"
                          class="q-ma-none"
                          :class="'text-' + getColorIndicador(loading ? null : totalizador.linhaemabertoindicador)"
                        >
                          {{loading ? '?' : $helpers.formatRS(totalizador.linhaemabertoindicador, false, 2) + '%'}}
                        </q-circular-progress>
                    </q-item-section>
                  </q-item>
                </q-card>
              </div>

              <!-- grafico status acompanhamneto -->
              <div class="col-xs-12 col-sm-6 col-md-3">
                <q-card class="text-grey-9 full-width bg-white" flat bordered >
                  <q-item>
                    <q-item-section>
                      <div class="fit row wrap full-width">
                        <div class="col-12 text-subtitle2 text-grey-7 ellipsis">Linhas em aberto</div>
                        <div class="col-12 text-subtitle2 text-grey-7 ellipsis">em acompanhamento</div>
                      </div>
                    </q-item-section>
                    <q-item-section >
                        <div class="fit row wrap justify-end full-width text-right">
                          <div class="col-12 text-h4 text-weight-bold" v-if="loading"><q-spinner color="indigo" /></div>
                          <div class="col-12 text-h4 text-weight-bold" v-if="!loading">{{$helpers.formatRS(totalizador.emabertopercentual.emacompanhamento, false, 0)}}</div>
                        </div>
                    </q-item-section>
                    <q-tooltip :delay="700" v-if="!loading">
                      <div class="text-subtitle2">Linhas em aberto e acompanhadas</div>
                      <div class="text-h4 text-weight-bold">{{$helpers.formatRS(totalizador.emabertopercentual.emacompanhamento, false, 0)}}</div>
                    </q-tooltip>
                  </q-item>
                  <q-separator />
                  <q-card-section class="bg-white text-black q-pa-none q-ma-none">
                    <div class="full-width row wrap justify-center items-center content-center">
                      <div class="col-12" v-if="chartlinhaemaberto.series ? chartlinhaemaberto.series.length > 0 : false"  style="min-height: 230px" >
                        <apex-simple-pie-chart :series="chartlinhaemaberto.series" :labels="chartlinhaemaberto.labels" :colors="chartlinhaemaberto.colors"></apex-simple-pie-chart>
                      </div>
                    </div>
                  </q-card-section>
                </q-card>
              </div>
              <!-- grafico status acompanhamneto -->

              <!-- grafico por status de processo -->
              <div class="col-xs-12 col-sm-6 col-md-3">
                <q-card class="text-grey-9 full-width bg-white" flat bordered>
                  <q-item>
                    <q-item-section>
                      <div class="fit row wrap full-width">
                        <div class="col-12 text-subtitle2 text-grey-7">Status por</div>
                        <div class="col-12 text-subtitle2 text-grey-7 ellipsis">processo</div>
                      </div>
                    </q-item-section>
                    <q-item-section >
                        <div class="fit row wrap justify-end full-width text-right">
                          <div class="col-12 text-h4 text-weight-bold" v-if="loading"><q-spinner color="indigo" /></div>
                          <div class="col-12 text-h4 text-weight-bold" v-if="!loading">
                            <span class="col-12 text-subtitle2 text-grey-6 q-mr-xs">erros</span>
                            {{$helpers.formatRS(totalizador.statusprocessopercentual.errosqtde, false, 0)}}
                          </div>
                        </div>
                    </q-item-section>
                    <q-tooltip :delay="700" v-if="!loading">
                      <div class="text-subtitle2">Linhas em aberto e acompanhadas</div>
                      <div class="text-h4 text-weight-bold">{{$helpers.formatRS(totalizador.statusprocessopercentual.errosqtde, false, 0)}}</div>
                    </q-tooltip>
                  </q-item>
                  <q-separator />
                  <q-card-section class="bg-white text-black q-pa-none q-ma-none">
                    <div class="full-width row wrap justify-center items-center content-center" >
                      <div class="col-12" v-if="chartstatusprocesso.series ? chartstatusprocesso.series.length > 0 : false"  style="min-height: 230px" >
                      <apex-simple-pie-chart :series="chartstatusprocesso.series" :labels="chartstatusprocesso.labels" :colors="chartstatusprocesso.colors"></apex-simple-pie-chart>
                      </div>
                    </div>
                  </q-card-section>
                </q-card>
              </div>
              <!-- grafico por erro -->

              <!-- listagem de erros 2 -->
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <q-card class="text-grey-9 full-width bg-white" flat bordered  >
                  <q-card-section class="bg-white text-black q-pa-xs">
                    <div class="fit row wrap full-width">
                      <div class="col-12">
                        <q-table :data="rowserros" :columns="columnserros" dense flat :loading="loading"
                          id="sticky-table" row-key="label" :rows-per-page-options="[0]" hide-pagination
                        >
                        <template v-slot:body-cell-saldoaberto="props">
                          <q-td :props="props">
                            <div>
                              {{$helpers.formatBigValue(props.row.saldoaberto, 3)}}
                            </div>
                            <q-tooltip :delay="700">
                              {{ $helpers.formatRS(props.row.saldoaberto) + '%'  }}
                            </q-tooltip>
                          </q-td>
                        </template>
                        <template v-slot:body-cell-label="props">
                          <q-td :props="props" class="text-caption">
                            <div class="ellipsis">
                              {{props.row.label}}
                              <q-badge text-color="black"
                                :label="props.row.tipo === 'D' ? 'Promessa' : (props.row.tipo === 'A' ? 'Agenda' : 'Coleta')"
                                :color="props.row.tipo === 'D' ? 'orange-1' : (props.row.tipo === 'A' ? 'green-2' : 'blue-1')"
                                />
                            </div>
                            <q-tooltip :delay="700">
                              <div>Tipo do erro: {{props.row.tipo === 'D' ? 'Data de promessa' : (props.row.tipo === 'A' ? 'Data do agendamento' : 'Data da coleta')}}</div>
                              <div>erro: {{props.row.label}}</div>
                            </q-tooltip>
                          </q-td>
                        </template>
                        <template v-slot:body-cell-indice="props">
                          <q-td :props="props">
                            <div class="ellipsis" style="min-width: 200px">
                              <q-linear-progress stripe rounded size="20px"
                                :value="parseFloat((props.row.indice/100).toFixed(2))"
                                :color="getColorIndicador(loading ? null : props.row.indice)"
                                class="q-mt-sm" >
                                <div class="absolute-full flex justify-end q-pr-sm">
                                  <span class="text-caption" :class="'text-' + getColorIndicador(loading ? null : props.row.indice, true)">
                                    {{$helpers.formatRS(props.row.indice, false, 2) + '%'}}
                                  </span>
                                </div>
                              </q-linear-progress>
                            </div>
                            <q-tooltip :delay="700">
                              {{ $helpers.formatRS(props.row.indice, false, 2) + '%'  }}
                            </q-tooltip>
                          </q-td>
                        </template>
                        </q-table>
                      </div>
                    </div>
                  </q-card-section>
                </q-card>
              </div>
              <!-- listagem de erros 2 -->

              <!-- listagem de dados -->
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <q-card class="text-grey-9 full-width bg-white" flat bordered  >
                  <q-card-section class="bg-white text-black q-pa-xs">
                    <div class="fit row wrap full-width">
                      <div class="col-12">
                        <q-table :data="rowshistorico" :columns="columnshistorico" dense flat :loading="loading"
                          id="sticky-table" row-key="label" :rows-per-page-options="[0]" hide-pagination
                        >
                        <template v-slot:body-cell-saldoaberto="props">
                          <q-td :props="props">
                            <div>
                              {{$helpers.formatBigValue(props.row.saldoaberto, 3)}}
                            </div>
                            <q-tooltip :delay="700">
                              {{ $helpers.formatRS(props.row.saldoaberto) + '%'  }}
                            </q-tooltip>
                          </q-td>
                        </template>
                        <template v-slot:body-cell-linhasabertovencidasindice="props">
                          <q-td :props="props" class="text-caption">
                            <div>
                              <q-linear-progress stripe rounded size="20px" :value="parseFloat((props.row.linhasabertovencidasindice/100).toFixed(2))"
                                :color="getColorIndicador(loading ? null : props.row.linhasabertovencidasindice)" class="q-mt-sm" >
                                <div class="absolute-full flex justify-end q-pr-sm">
                                  <span class="text-caption" :class="'text-' + getColorIndicador(loading ? null : props.row.linhasabertovencidasindice, true)">
                                    {{$helpers.formatRS(props.row.linhasabertovencidasindice, false, 2) + '%'}}
                                  </span>
                                </div>
                              </q-linear-progress>
                            </div>
                            <q-tooltip :delay="700">
                              {{ $helpers.formatRS(props.row.linhasabertovencidasindice, false, 2) + '%'  }}
                            </q-tooltip>
                          </q-td>
                        </template>
                        <template v-slot:body-cell-saldoabertovencidasindice="props">
                          <q-td :props="props" class="text-caption">
                            <div>
                              <q-linear-progress stripe rounded size="20px" :value="parseFloat((props.row.saldoabertovencidasindice/100).toFixed(2))"
                                :color="getColorIndicador(loading ? null : props.row.saldoabertovencidasindice)" class="q-mt-sm" >
                                <div class="absolute-full flex justify-end q-pr-sm">
                                  <span class="text-caption" :class="'text-' + getColorIndicador(loading ? null : props.row.saldoabertovencidasindice, true)">
                                    {{$helpers.formatRS(props.row.saldoabertovencidasindice, false, 2) + '%'}}
                                  </span>
                                </div>
                              </q-linear-progress>
                            </div>
                            <q-tooltip :delay="700">
                              {{ $helpers.formatRS(props.row.saldoabertovencidasindice, false, 2) + '%'  }}
                            </q-tooltip>
                          </q-td>
                        </template>
                        </q-table>
                      </div>
                    </div>
                  </q-card-section>
                </q-card>
              </div>
              <!-- listagem de dados -->
            </div>
          </q-card-section>
        </q-card>
      </div>
      <q-page-sticky position="top" expand>
        <q-toolbar class="bg-white text-primary shadow-up-21">
          <q-btn flat dense round @click="$store.dispatch('homedashboard/togglemenu')" aria-label="Menu" icon="menu" />
          <q-btn flat round icon="arrow_back" @click="$router.go(-1)"/>
          <q-separator vertical inset v-if="!$q.platform.is.mobile" spaced/>
          <q-toolbar-title>Indicadores de Desempenho</q-toolbar-title>
          <q-btn  icon="sync" :label="$q.platform.is.mobile ? '' : 'Atualizar'" :round="$q.platform.is.mobile" flat @click="refreshData" :loading="loading" />
        </q-toolbar>
      </q-page-sticky>
    </div>
  </q-page>
</div>
</template>

<script>
import FollowUps from 'src/mvc/collections/followups.js'
export default {
  name: 'followup.dashboard1',
  components: {
    ApexSimplePieChart: () => import('src/components/apexcharts/ApexSimplePieChart')
  },
  data () {
    var dataset = new FollowUps()
    return {
      dataset,
      error: null,
      usuario: null,
      rows: [],
      totalizador: null,
      chartlinhaemaberto: {
        series: [],
        labels: []
      },
      chartstatusprocesso: {
        series: []
      },
      rowshistorico: [],
      columnshistorico: [
        { name: 'label', align: 'left', label: 'Tempo em aberto O.C.', field: 'label' },
        { name: 'linhasaberto', align: 'center', label: 'Linhas', field: 'linhasaberto' },
        { name: 'linhasabertovencidasindice', align: 'right', label: 'Linhas vencidas %', field: 'linhasabertovencidasindice' },
        { name: 'saldoaberto', align: 'right', label: 'Saldo R$', field: 'saldoaberto' },
        { name: 'saldoabertovencidasindice', align: 'right', label: '% Saldo vencido', field: 'saldoabertovencidasindice' }
      ],
      rowserros: [],
      columnserros: [
        { name: 'label', align: 'left', label: 'Erro', field: 'label' },
        { name: 'qtde', align: 'center', label: 'Qtde', field: 'qtde' },
        { name: 'indice', align: 'right', label: '%', field: 'indice' }
      ],
      loading: true
    }
  },
  async mounted () {
    var app = this
    app.usuario = null
    if (app.$store.state.usuario.logado) {
      if (app.$store.state.usuario.user) {
        app.usuario = app.$store.state.usuario.user
      }
    }
    await app.refreshData(null)
  },
  methods: {
    getColorIndicador (v, text = false) {
      var color = text ? 'black' : 'blue'
      if (!v) return color
      if (parseInt(v) > 33) color = text ? 'black' : 'yellow'
      if (parseInt(v) > 66) color = text ? 'white' : 'red'
      return color
    },
    async actNewDialog (rowIndex) {
      var app = this
      var row = new FollowUps()
      var ret = await row.dialogAddOrEdit(app)
      if (ret) {
        app.dateinicial = ret.valuei
        app.datefinal = ret.valuef
        await app.refreshData()
      } else {
        console.log(ret)
        if (ret.msg ? ret.msg !== '' : false) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {
          })
        }
      }
    },
    async refreshData () {
      var app = this
      app.loading = true
      try {
        app.error = null
        if (!app.usuario) throw new Error('Nenhum usuário logado')
        if (!app.usuario.pemitefup) throw new Error('Sem permissão de acesso')

        var ret = await app.dataset.fetchDashboard1(app)
        if (ret.ok) {
          app.rows = ret.data
          app.totalizador = app.rows.totalizadores

          app.rowshistorico = app.rows.historicolista

          app.chartlinhaemaberto.labels = ['Em acompanhamento', 'Em aberto']
          app.chartlinhaemaberto.series = [app.totalizador.emabertopercentual.emacompanhamentoperc, app.totalizador.emabertopercentual.emabertoperc]
          app.chartlinhaemaberto.colors = ['#64dd17', '#f44336']

          app.chartstatusprocesso.labels = ['Com erros', 'OK']
          app.chartstatusprocesso.series = [app.totalizador.statusprocessopercentual.errosperc, app.totalizador.statusprocessopercentual.okperc]
          app.chartstatusprocesso.colors = ['#f44336', '#64dd17']

          app.rowserros = []
          for (let index = 0; index < app.rows.erroslista.length; index++) {
            const element = app.rows.erroslista[index]
            app.rowserros.push({ tipo: element.tipo, label: element.descricao, qtde: element.total, indice: element.indice })
          }
        } else {
          if (ret.msg ? ret.msg !== '' : false) {
            var a = app.$helpers.showDialog(ret)
            await a.then(function () {
            })
          }
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
