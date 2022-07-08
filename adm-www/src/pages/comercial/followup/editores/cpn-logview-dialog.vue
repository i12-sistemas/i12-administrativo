<template>
  <q-dialog ref="dialog" @hide="onDialogHide" :maximized="$q.platform.is.mobile">
    <q-card class="q-dialog-plugin" :class="$q.platform.is.mobile ? '' : 'full-90'">
      <q-bar class="bg-primary text-white" v-if="!$q.platform.is.mobile">
        <span class="ellipsis text-subtitle2 q-ml-xs">{{title}}</span>
        <q-space />
        <q-btn dense flat icon="close" @click="actClose">
          <q-tooltip>Sair sem salvar (ESC)</q-tooltip>
        </q-btn>
      </q-bar>
      <q-toolbar class="bg-primary text-white" v-if="$q.platform.is.mobile">
        <q-toolbar-title>{{title}}</q-toolbar-title>
        <q-btn flat round dense icon="close" @click="actClose"/>
      </q-toolbar>

      <q-card-section>
        <div class="col-sm-12" v-if="rows" id="sticky-table-scroll">
          <q-table :data="rows" :columns="columns" dense bordered flat
            :loading="loading"
            id="sticky-table"
            :pagination.sync="logs.pagination"
            row-key="id"
            :rows-per-page-options="$qtable.rowsperpageoptions"
            @request="refreshData"
            title="Log de alteração"
            >
            <template v-slot:loading>
              <q-linear-progress indeterminate />
            </template>
              <template v-slot:top-right>
                <q-btn color="black" outline  label="exportar" class="q-mr-sm" :disable="loading" >
                <q-menu anchor="bottom right" self="top right">
                  <q-list style="min-width: 100px" dense>
                    <q-item clickable v-close-popup @click="actExportar('xlsx')">
                      <q-item-section>Exportar para Excel (xlsx)</q-item-section>
                      <q-item-section avatar>
                        <q-avatar icon="fas fa-file-excel" />
                        </q-item-section>
                    </q-item>
                    <q-item clickable v-close-popup @click="actExportar('csv')">
                      <q-item-section>Exportar para CSV (csv)</q-item-section>
                      <q-item-section avatar>
                        <q-avatar icon="fas fa-file-csv" />
                        </q-item-section>
                    </q-item>
                  </q-list>
                </q-menu>
              </q-btn>
                <q-btn color="black" icon="sync"  @click="refreshData(null)"  outline :loading="loading" />
              </template>
              <!-- rows -->
              <template v-slot:body-cell-created_at="props">
                <q-td :props="props" class="text-caption">
                  <div>
                    {{ props.row.created_at ? $helpers.datetimeToBR(props.row.created_at, true, true) : '' }}
                    <q-tooltip :delay="700">
                      {{ props.row.created_at ? $helpers.datetimeToBR(props.row.created_at) : '' }}
                    </q-tooltip>
                  </div>
                </q-td>
              </template>
              <template v-slot:body-cell-tipoorigem="props">
                <q-td :props="props" class="text-caption" :class="'text-' + props.row.tipoorigem.color">
                  <div>
                    <q-icon :name="props.row.tipoorigem.icon" size="20px" />
                    {{ props.row.tipoorigem.label }}
                    <q-tooltip :delay="700">
                      <div>{{props.row.tipoorigem.description}}</div>
                    </q-tooltip>
                  </div>
                </q-td>
              </template>
              <template v-slot:body-cell-created_usuario="props">
                <q-td :props="props" class="text-caption">
                  <div>
                    {{ props.row.created_usuario ? props.row.created_usuario.nome : 'Não identificado' }}
                    <q-tooltip :delay="700">
                      {{ props.row.created_usuario ? props.row.created_usuario.nome : 'Não identificado' }}
                    </q-tooltip>
                  </div>
                </q-td>
              </template>
              <template v-slot:body-cell-erroagendastatus="props">
                <q-td :props="props" :class="'bg-' + props.row.erroagendastatus.color + '-2 text-' + props.row.erroagendastatus.color + '-10'" class="text-caption">
                  <div style="width: 70px" class="ellipsis" >
                    <span v-if="props.row.erroagendastatus.value === '2'">{{ props.row.erroagenda ? props.row.erroagenda.descricao : '?' }}</span>
                    <span v-else>{{ props.row.erroagendastatus.description }}</span>
                    <q-tooltip :delay="700">
                      <div v-if="props.row.erroagendastatus.value === '2'">Erro: {{ props.row.erroagenda ? props.row.erroagenda.descricao : '** Não identificado **' }}</div>
                      <span v-else>Cód.: {{ props.row.erroagendastatus }} - {{ props.row.erroagendastatus.description }}</span>
                    </q-tooltip>
                  </div>
                </q-td>
              </template>
              <template v-slot:body-cell-datasolicitacao="props">
                <q-td :props="props">
                  <div class="ellipsis" >
                    {{ props.row.datasolicitacao ? $helpers.dateToBR(props.row.datasolicitacao) : '-' }}
                    <q-tooltip :delay="700" v-if="props.row.datasolicitacao ">
                      <div>{{ $helpers.dateToBR(props.row.datasolicitacao) }}</div>
                      <div>Clique aqui para editar</div>
                    </q-tooltip>
                  </div>
                </q-td>
              </template>
              <template v-slot:body-cell-dataagendamentocoleta="props">
                <q-td :props="props">
                  <div class="ellipsis" >
                    {{ props.row.dataagendamentocoleta ? $helpers.dateToBR(props.row.dataagendamentocoleta) : '-' }}
                    <q-tooltip :delay="700" >
                      <div v-if="props.row.dataagendamentocoleta" >{{ $helpers.dateToBR(props.row.dataagendamentocoleta) }}</div>
                    </q-tooltip>
                  </div>
                </q-td>
              </template>
              <template v-slot:body-cell-dataconfirmacao="props">
                <q-td :props="props">
                  <div class="ellipsis" >
                    {{ props.row.dataconfirmacao ? $helpers.dateToBR(props.row.dataconfirmacao) : '-' }}
                    <q-tooltip :delay="700" >
                      <div v-if="props.row.dataconfirmacao" >{{ $helpers.dateToBR(props.row.dataconfirmacao) }}</div>
                    </q-tooltip>
                  </div>
                </q-td>
              </template>
              <template v-slot:body-cell-datacoleta="props">
                <q-td :props="props">
                  <div class="ellipsis" >
                    {{ props.row.datacoleta ? $helpers.dateToBR(props.row.datacoleta) : '-' }}
                    <q-tooltip :delay="700" >
                      <div v-if="props.row.datacoleta" >{{ $helpers.dateToBR(props.row.datacoleta) }}</div>
                    </q-tooltip>
                  </div>
                </q-td>
              </template>
              <template v-slot:body-cell-coletaid="props">
                <q-td :props="props" >
                  <div class="ellipsis" >
                    {{ props.row.coletaid ? props.row.coletaid : '-' }}
                    <q-tooltip :delay="700" >
                      <div v-if="props.row.coletaid" >Coleta #{{ props.row.coletaid }}</div>
                    </q-tooltip>
                  </div>
                </q-td>
              </template>
              <template v-slot:body-cell-notafiscal="props">
                <q-td :props="props" >
                  <div class="ellipsis" >
                    {{ props.row.notafiscal ? props.row.notafiscal : '-' }}
                    <q-tooltip :delay="700" >
                      <div v-if="props.row.coletaid" >Nota Fiscal #{{ props.row.notafiscal }}</div>
                    </q-tooltip>
                  </div>
                </q-td>
              </template>
              <template v-slot:body-cell-errocoletastatus="props">
                <q-td :props="props" :class="'bg-' + props.row.errocoletastatus.color + '-2 text-' + props.row.errocoletastatus.color + '-10'" class="text-caption">
                  <div style="width: 70px" class="ellipsis" >
                    <span v-if="props.row.errocoletastatus.value === '2'">{{ props.row.errocoleta ? props.row.errocoleta.descricao : '?' }}</span>
                    <span v-else>{{ props.row.errocoletastatus.description }}</span>
                    <q-tooltip :delay="700">
                      <div v-if="props.row.errocoletastatus.value === '2'">Erro: {{ props.row.errocoleta ? props.row.errocoleta.descricao : '** Não identificado **' }}</div>
                      <span v-else>Cód.: {{ props.row.errocoletastatus }} - {{ props.row.errocoletastatus.description }}</span>
                    </q-tooltip>
                  </div>
                </q-td>
              </template>
              <template v-slot:body-cell-observacao="props">
                <q-td :props="props" class="text-caption">
                  <div style="max-width: 150px" class="ellipsis" >
                    {{ props.row.observacao ? props.row.observacao : '-' }}
                    <q-tooltip :delay="700">
                      <div v-if="props.row.observacao ? props.row.observacao !== '' : false" style="white-space: pre-line;">{{ props.row.observacao }}</div>
                    </q-tooltip>
                  </div>
                </q-td>
              </template>
              <template v-slot:body-cell-statusconfirmacaocoleta="props">
                <q-td :props="props" :class="'bg-' + props.row.statusconfirmacaocoleta.color + '-2 text-' + props.row.statusconfirmacaocoleta.color + '-10'" class="text-caption">
                  <div style="width: 70px" class="ellipsis" >
                    <span >{{ props.row.statusconfirmacaocoleta.description }}</span>
                    <q-tooltip :delay="700">
                      <span>Cód.: {{ props.row.statusconfirmacaocoleta }} - {{ props.row.statusconfirmacaocoleta.description }}</span>
                    </q-tooltip>
                  </div>
                </q-td>
              </template>
              <template v-slot:body-cell-iniciofollowup="props">
                <q-td :props="props" :class="'bg-' + props.row.iniciofollowup.color + '-2 text-' + props.row.iniciofollowup.color + '-10'" class="text-caption">
                  <div style="width: 70px" class="ellipsis" >
                    <span >{{ props.row.iniciofollowup.description }}</span>
                    <q-tooltip :delay="700">
                      <span>Cód.: {{ props.row.iniciofollowup }} - {{ props.row.iniciofollowup.description }}</span>
                    </q-tooltip>
                  </div>
                </q-td>
              </template>
              <template v-slot:body-cell-errodtpromessastatus="props">
                <q-td :props="props" :class="'bg-' + props.row.errodtpromessastatus.color + '-2 text-' + props.row.errodtpromessastatus.color + '-10'" class="text-caption">
                  <div style="width: 70px" class="ellipsis" >
                    <span v-if="props.row.errodtpromessastatus.value === '2'">{{ props.row.errodtpromessa ? props.row.errodtpromessa.descricao : '?' }}</span>
                    <span v-else>{{ props.row.errodtpromessastatus.description }}</span>
                    <q-tooltip :delay="700">
                      <div v-if="props.row.errodtpromessastatus.value === '2'">Erro: {{ props.row.errodtpromessa ? props.row.errodtpromessa.descricao : '** Não identificado **' }}</div>
                      <span v-else>Cód.: {{ props.row.errodtpromessastatus }} - {{ props.row.errodtpromessastatus.description }}</span>
                    </q-tooltip>
                  </div>
                </q-td>
              </template>
              <!-- rows -->
          </q-table>
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import Vue from 'vue'
import FollowUpsLog from 'src/mvc/collections/followupslogs.js'
export default {
  name: 'comercial.fup.logview.dialog',
  components: {
  },
  props: ['dataset'],
  data () {
    var logs = new FollowUpsLog()
    return {
      logs,
      rows: [],
      title: 'Carregando...',
      loading: true,
      tevealteracao: false,
      columns: [
        { name: 'tipoorigem', align: 'left', label: 'Origem', field: 'tipoorigem' },
        { name: 'created_at', align: 'center', label: 'Data alteração', field: 'created_at' },
        { name: 'created_usuario', align: 'left', label: 'Usuário', field: 'created_usuario' },
        { name: 'datasolicitacao', align: 'center', label: 'Data Solicitação', format: (val, row) => val ? Vue.prototype.$helpers.dateToBR(val) : '', field: 'datasolicitacao' },
        { name: 'datapromessa', align: 'center', label: 'Data Promessa', field: 'datapromessa', format: (val, row) => val ? Vue.prototype.$helpers.dateToBR(val) : '' },
        { name: 'erroagendastatus', align: 'left', label: 'St. Agenda', field: 'erroagendastatus' },
        { name: 'errodtpromessastatus', align: 'left', label: 'St. Promessa', field: 'errodtpromessastatus' },
        { name: 'errocoletastatus', align: 'left', label: 'St. Coleta', field: 'errocoletastatus' },
        { name: 'iniciofollowup', align: 'left', label: 'Início FUP', field: 'iniciofollowup' },
        { name: 'notafiscal', align: 'center', label: 'Nota Fiscal', field: 'notafiscal' },
        { name: 'totallinhaoc', align: 'right', label: 'Total Linha OC', field: 'totallinhaoc', format: (val, row) => Vue.prototype.$helpers.formatRS(val, false, 2) },
        { name: 'qtdesolicitada', align: 'right', label: 'Qtde Sol.', field: 'qtdesolicitada' },
        { name: 'qtderecebida', align: 'right', label: 'Qtde Receb', field: 'qtderecebida' },
        { name: 'qtdedevida', align: 'right', label: 'Qtde Dev', field: 'qtdedevida' },
        { name: 'observacao', align: 'left', label: 'Observação', field: 'observacao' },
        { name: 'itemnumerolinhapedido', align: 'center', label: 'Nº linha do item', field: 'itemnumerolinhapedido', style: 'max-width: 100px;', classes: 'ellipsis' },
        { name: 'statusconfirmacaocoleta', align: 'left', label: 'St. Confirmação', field: 'statusconfirmacaocoleta' },
        { name: 'dataagendamentocoleta', align: 'center', label: 'Agenda Coleta', field: 'dataagendamentocoleta', format: (val, row) => val ? Vue.prototype.$helpers.dateToBR(val) : '' },
        { name: 'dataconfirmacao', align: 'center', label: 'Data Confirmação', field: 'dataconfirmacao', format: (val, row) => val ? Vue.prototype.$helpers.dateToBR(val) : '' },
        { name: 'coletaid', align: 'center', label: 'Nº da Coleta', field: 'coletaid' },
        { name: 'datacoleta', align: 'center', label: 'Data Coleta', field: 'datacoleta', format: (val, row) => val ? Vue.prototype.$helpers.dateToBR(val) : '' },
        { name: 'ordemcompra', align: 'center', label: 'O.C.', field: 'ordemcompra' },
        { name: 'ordemcompradig', align: 'center', label: 'Dígito', field: 'ordemcompradig' }
      ]
    }
  },
  async mounted () {
    var app = this
    app.title = 'Detalhe de alteração' + ((app.dataset ? app.dataset.id > 0 : false) ? ' :: registro id ' + app.dataset.id : '')
    app.refreshData(null)
  },
  methods: {
    async actExportar (format = 'xlsx') {
      var app = this
      app.loading = true
      var ret = await app.logs.showExportListagem(app.dataset.id, app, format)
      if (!ret.ok) {
        if (ret.msg ? ret.msg !== '' : false) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {
          })
        }
      }
      app.loading = false
    },
    async refreshData (props) {
      var app = this
      app.loading = true
      app.logs.readPropsTable(props)
      if (app.$q.platform.is.mobile) app.logs.pagination.rowsPerPage = 50
      app.logs.query = {
        perpage: app.logs.pagination.rowsPerPage,
        page: app.logs.pagination.page
      }
      var ret = await app.logs.fetch(app.dataset.id)
      if (ret.ok) {
        app.rows = app.logs.itens
      } else {
        if (ret.msg ? ret.msg !== '' : false) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {
          })
        }
      }
      app.loading = false
    },
    setTitle (val) {
      this.title = val
    },
    ontevealteracao (val) {
      this.tevealteracao = val
    },
    actClose () {
      this.onClose(this.tevealteracao)
    },
    onClose (TeveAlteracao) {
      this.$emit('ok', TeveAlteracao)
      this.hide()
    },
    async actUpdated (dataset) {
      // var app = this
      // app.dataset = dataset
    },

    // following method is REQUIRED
    // (don't change its name --> "show")
    show () {
      var app = this
      this.$refs.dialog.show()
      setTimeout(() => {
        app.loading = false
      }, 500)
    },

    // following method is REQUIRED
    // (don't change its name --> "hide")
    hide () {
      this.$refs.dialog.hide()
    },

    onDialogHide () {
      // required to be emitted
      // when QDialog emits "hide" event
      this.$emit('hide')
    },

    onOKClick () {
      // if (!this.aceite) return
      // if (!(this.justificativa.length > 5)) return
      // on OK, it is REQUIRED to
      // emit "ok" event (with optional payload)
      // before hiding the QDialog
      this.$emit('ok', true)
      // or with payload: this.$emit('ok', { ... })

      // then hiding dialog
      this.hide()
    }

  }
}
</script>
