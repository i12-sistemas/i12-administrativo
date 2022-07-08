<template>
<div>
  <q-page class="">
    <div class="row q-ma-lg" v-if="!$q.platform.is.mobile"  >
      <div class="col-sm-12" id="sticky-table-scroll">
        <q-table :data="rows" :columns="columns" dense
          :loading="loading"
          id="sticky-table"
          :pagination.sync="dataset.pagination"
          row-key="id"
          :rows-per-page-options="$qtable.rowsperpageoptions"
          @request="refreshData"
          :filter="filter"
          title="Consulta Nota Fiscal"
          >
          <template v-slot:loading>
            <q-linear-progress indeterminate />
          </template>
          <template v-slot:no-data="{ icon, message, filter }">
            <div class="full-width row wrap flex-center text-grey-9 q-gutter-sm">
              <div class="col-12 q-ma-md text-center text-subtitle2 q-mt-lg">
                Digite alguma informação campo "Procurar" para pesquisar notas!
              </div>
              <div class="col-12 q-ma-md text-center">
                <q-icon size="34px" :name="filter ? 'filter_b_and_w' : icon" />
              </div>
              <div class="col-12 q-ma-md text-center">
                {{ message }}
              </div>
            </div>
          </template>
          <!-- headers -->
          <template v-slot:header-cell="props">
              <q-th :props="props" class="" >
                <div>
                  <div>
                    {{ props.col.label }}
                  </div>
                  <div v-if="props.col.filterconfig">
                    <div v-if="props.col.filterconfig.filtertype ? props.col.filterconfig.filtertype === 'text' : false">
                      <q-input type="text" input-class="text-accent text-uppercase" color="accent" dense v-model="props.col.filterconfig.value" clearable @input="actInputFilter()"
                        :debounce="700" :loading="loading" :disable="loading"
                        >
                        <template v-slot:prepend v-if="props.col.filterconfig.filtertype2 ? props.col.filterconfig.filtertype2 !== '' : false" >
                          <q-btn icon="filter_alt" round dense size="xs" unelevated @click="actShowFilter2(props)"
                            :color="(props.col.filterconfig.value2 ? 'accent' : 'grey-3')"
                            :text-color="(props.col.filterconfig.value2 ? 'white' : 'grey-7')"
                          >
                            <q-tooltip :delay="700" v-if="props.col.filterconfig.value2" >
                              <div v-html="props.col.filterconfig.value2"></div>
                            </q-tooltip>
                          </q-btn>
                        </template>
                        <q-tooltip :delay="700" v-if="props.col.filterconfig.tooltip ? props.col.filterconfig.tooltip !== '' : false" >
                          <div v-html="props.col.filterconfig.tooltip"></div>
                        </q-tooltip>
                      </q-input>
                    </div>
                    <div v-if="props.col.filterconfig.filtertype ? props.col.filterconfig.filtertype === 'floatinterval' : false">
                      <div v-if="props.col.filterconfig.value !=null" >
                        <q-chip label="Filtrado" dense color="accent" text-color="white" clickable  @click="actShowFilter(props)"
                          removable @remove="actClearFilter(props)" class="text-caption truncate-chip-labels "
                          style="max-width: 100px"
                          >
                          <div class="ellipsis">
                            <span v-if="props.col.filterconfig.value.valuei" v-html="'>=' + $helpers.formatRS(props.col.filterconfig.value.valuei, (props.col.filterconfig.prefix ? props.col.filterconfig.prefix == 'R$' : false), (props.col.filterconfig.decimal ? props.col.filterconfig.decimal : 0))" />
                            <span v-if="props.col.filterconfig.value.valuei && props.col.filterconfig.value.valuef"> à </span>
                            <span v-if="props.col.filterconfig.value.valuef" v-html="'<=' + $helpers.formatRS(props.col.filterconfig.value.valuef, (props.col.filterconfig.prefix ? props.col.filterconfig.prefix == 'R$' : false), (props.col.filterconfig.decimal ? props.col.filterconfig.decimal : 0))" />
                          </div>
                          <q-tooltip :delay="1000" >
                            <span v-if="props.col.filterconfig.value.valuei">De {{$helpers.formatRS(props.col.filterconfig.value.valuei, (props.col.filterconfig.prefix ? props.col.filterconfig.prefix == 'R$' : false), (props.col.filterconfig.decimal ? props.col.filterconfig.decimal : 0))}}</span>
                            <span v-if="props.col.filterconfig.value.valuei && props.col.filterconfig.value.valuef"> à </span>
                            <span v-if="props.col.filterconfig.value.valuef">{{$helpers.formatRS(props.col.filterconfig.value.valuef,  (props.col.filterconfig.prefix ? props.col.filterconfig.prefix == 'R$' : false), (props.col.filterconfig.decimal ? props.col.filterconfig.decimal : 0))}}</span>
                          </q-tooltip>
                        </q-chip>
                      </div>
                      <div v-else >
                        <q-chip label="filtrar" dense color="grey-3" text-color="grey-7" clickable  @click="actShowFilter(props)" class="text-caption" icon="filter_alt"  />
                      </div>
                    </div>
                    <div v-if="props.col.filterconfig.filtertype ? props.col.filterconfig.filtertype === 'datetimeinterval' : false">
                      <div v-if="props.col.filterconfig.value !=null" >
                        <q-chip label="Filtrado" dense color="accent" text-color="white" clickable  @click="actShowFilter(props)"
                          removable @remove="actClearFilter(props)" class="text-caption truncate-chip-labels "
                          style="max-width: 100px"
                          >
                          <div class="ellipsis">
                            <span v-if="props.col.filterconfig.value.valuei" v-html="'>=' + $helpers.dateToBR(props.col.filterconfig.value.valuei)" />
                            <span v-if="props.col.filterconfig.value.valuei && props.col.filterconfig.value.valuef"> à </span>
                            <span v-if="props.col.filterconfig.value.valuef" v-html="'<=' + $helpers.dateToBR(props.col.filterconfig.value.valuef)" />
                          </div>
                          <q-tooltip :delay="1000" >
                            <span v-if="props.col.filterconfig.value.valuei">De {{$helpers.dateToBR(props.col.filterconfig.value.valuei)}}</span>
                            <span v-if="props.col.filterconfig.value.valuei && props.col.filterconfig.value.valuef"> à </span>
                            <span v-if="props.col.filterconfig.value.valuef">{{$helpers.dateToBR(props.col.filterconfig.value.valuef)}}</span>
                          </q-tooltip>
                        </q-chip>
                      </div>
                      <div v-else >
                        <q-chip label="filtrar" dense color="grey-3" text-color="grey-7" clickable  @click="actShowFilter(props)" class="text-caption" icon="filter_alt"  />
                      </div>
                    </div>
                  </div>
                </div>
              </q-th>
          </template>
          <!-- headers -->
          <template v-slot:top-right>
            <q-btn color="black" outline  icon="print" class="q-mr-sm" >
                <q-menu anchor="bottom right" self="top right">
                  <q-list style="min-width: 100px" dense>
                    <q-item clickable v-close-popup @click="actImprimirListagem('pdf')" v-if="false">
                      <q-item-section>Imprimir listagem (pdf)</q-item-section>
                      <q-item-section avatar>
                        <q-avatar icon="print" />
                        </q-item-section>
                    </q-item>
                    <q-item clickable v-close-popup @click="actImprimirListagem('xlsx')">
                      <q-item-section>Exportar para Excel (xlsx)</q-item-section>
                      <q-item-section avatar>
                        <q-avatar icon="fas fa-file-excel" />
                        </q-item-section>
                    </q-item>
                    <q-item clickable v-close-popup @click="actImprimirListagem('csv')">
                      <q-item-section>Exportar para CSV (csv)</q-item-section>
                      <q-item-section avatar>
                        <q-avatar icon="fas fa-file-csv" />
                        </q-item-section>
                    </q-item>
                 </q-list>
               </q-menu>
             </q-btn>
            <q-input outlined dense debounce="500" v-model="filter" placeholder="Procurar" clearable>
              <template v-slot:append>
                <q-btn color="grey" icon="sync"  @click="refreshData(null)" dense round flat :loading="loading" />
              </template>
            </q-input>
          </template>
          <template v-slot:body-cell-numero="props">
            <q-td :props="props" >
              <div class="cursor-pointer"  @click="showNotaPDF(props.row.chave_acesso)">
                {{ props.row.numero }}
              </div>
            </q-td>
          </template>
          <template v-slot:body-cell-id="props">
            <q-td :props="props" >
              <div class="cursor-pointer"  @click="showNotaPDF(props.row.chave_acesso)">
                {{ props.row.id }}
              </div>
            </q-td>
          </template>
          <template v-slot:body-cell-data_emissao="props">
            <q-td :props="props" >
              <div class="cursor-pointer"  @click="showNotaPDF(props.row.chave_acesso)">
                {{ $helpers.dateToBR(props.row.data_emissao) }}
                <q-tooltip>
                  <div>{{ $helpers.datetimeRelativeToday(props.row.data_emissao) }}</div>
                </q-tooltip>
              </div>
            </q-td>
          </template>
          <template v-slot:body-cell-chave_acesso="props">
            <q-td :props="props" >
              <div class="cursor-pointer"  @click="showNotaPDF(props.row.chave_acesso)">
                <span class="text-caption">{{ props.row.chave_acesso }}</span>
              </div>
            </q-td>
          </template>
          <template v-slot:body-cell-peso="props">
            <q-td :props="props" >
              <div class="cursor-pointer" @click="showNotaPDF(props.row.chave_acesso)" >
                {{ $helpers.formatRS(props.row.peso, false, 3) }}
                <q-tooltip>
                  <div>Peso: {{ $helpers.formatRS(props.row.peso, false, 3) }}</div>
                </q-tooltip>
              </div>
            </q-td>
          </template>
          <template v-slot:body-cell-emitente_cnpj="props">
            <q-td :props="props" >
              <div class="cursor-pointer" @click="showNotaPDF(props.row.chave_acesso)" >
                {{ $helpers.mascaraDocCPFCNPJ(props.row.emitente_cnpj) }}
              </div>
            </q-td>
          </template>
          <template v-slot:body-cell-destinatario_cnpj="props">
            <q-td :props="props" >
              <div class="cursor-pointer" @click="showNotaPDF(props.row.chave_acesso)" >
                {{ $helpers.mascaraDocCPFCNPJ(props.row.destinatario_cnpj) }}
              </div>
            </q-td>
          </template>
          <template v-slot:body-cell-valor="props">
            <q-td :props="props" >
              <div class="cursor-pointer"  @click="showNotaPDF(props.row.chave_acesso)">
                {{ $helpers.formatRS(props.row.valor, false) }}
                <q-tooltip>
                  <div>{{ $helpers.formatRS(props.row.valor, true) }}</div>
                </q-tooltip>
              </div>
            </q-td>
          </template>
          <template v-slot:body-cell-emitente_nome="props">
            <q-td :props="props" >
              <div class="cursor-pointer ellipsis" style="max-width: 250px"  @click="showNotaPDF(props.row.chave_acesso)">
                {{ props.row.emitente_nome }}
                <q-tooltip>
                  <div>{{ props.row.emitente_nome }}</div>
                </q-tooltip>
              </div>
            </q-td>
          </template>
          <template v-slot:body-cell-destinatario_nome="props">
            <q-td :props="props" >
              <div class="cursor-pointer ellipsis" style="max-width: 250px"  @click="showNotaPDF(props.row.chave_acesso)">
                {{ props.row.destinatario_nome }}
                <q-tooltip>
                  <div>{{ props.row.destinatario_nome }}</div>
                </q-tooltip>
              </div>
            </q-td>
          </template>
        </q-table>
      </div>
    </div>
  </q-page>
</div>
</template>

<script>
import NFes from 'src/mvc/collections/nfes.js'
import NFe from 'src/mvc/models/nfe.js'
import Vue from 'vue'
export default {
  name: 'nfe.consulta.detalhe',
  components: {
  },
  data () {
    var dataset = new NFes()
    return {
      dataset,
      loading: false,
      filter: '',
      rows: [],
      columns: [
        { name: 'numero', align: 'left', label: 'Número', field: 'numero', style: 'max-width: 150px', filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'data_emissao', align: 'center', label: 'Data emissão', field: 'data_emissao', filterconfig: { filtertype: 'text', value: null, sorted: true } },
        // { name: 'chave_acesso', align: 'center', label: 'Chave de Acesso', field: 'chave_acesso' },
        { name: 'emitente_cnpj', align: 'left', label: 'Emitente CNPJ', field: 'emitente_cnpj', filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'emitente_nome', align: 'left', label: 'Emitente Nome', field: 'emitente_nome', filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'destinatario_cnpj', align: 'left', label: 'Dest. CNPJ', field: 'destinatario_cnpj', filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'destinatario_nome', align: 'left', label: 'Dest. Nome', field: 'destinatario_nome', filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'peso', style: 'width: 50px', align: 'right', label: 'Peso', field: 'peso', filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'valor', style: 'width: 100px', align: 'right', label: 'Valor', field: 'valor', filterconfig: { filtertype: 'text', value: null, sorted: true } },
        { name: 'id', align: 'center', label: 'ID', field: 'id' }
      ]
    }
  },
  async mounted () {
    var app = this
    if (app.$route.query) {
      await app.queryRead(app.$route.query)
      if (app.$route.query.t) app.refreshData()
    }
  },
  methods: {
    async actImprimirListagem (pForma) {
      var app = this
      await app.dataset.export(app, pForma)
    },
    async queryRead () {
      var app = this
      var pQuery = app.$route.query
      if (pQuery) {
        if (typeof pQuery.find !== 'undefined') app.filter = pQuery.find
        if (pQuery.pagesize) {
          var pagesize = parseInt(pQuery.pagesize)
          if (app.$qtable.rowsperpageoptions.indexOf(pagesize) >= 0) app.dataset.pagination.rowsPerPage = pagesize
        }
        if (pQuery.page) app.dataset.pagination.page = parseInt(pQuery.page)

        var sortby = null
        if (pQuery.sortby) {
          sortby = JSON.parse(pQuery.sortby)
          if (Object.keys(sortby).length > 0) app.orderbylist = sortby
        }

        for (let index = 0; index < app.columns.length; index++) {
          const element = app.columns[index]
          if (element.filterconfig) {
            if (element.filterconfig.filtertype2 !== '') {
              if ((pQuery[element.name + '2']) && (pQuery[element.name + '2'] !== '')) {
                if (element.filterconfig.datatype2 === JSON) {
                  element.filterconfig.value2 = JSON.parse(pQuery[element.name + '2'].split(','))
                } else {
                  element.filterconfig.value2 = pQuery[element.name + '2'].split(',').map(element.filterconfig.datatype2 ? element.filterconfig.datatype2 : Number)
                }
              }
            }
            if (element.filterconfig.filtertype === 'text') {
              if ((pQuery[element.name]) && (pQuery[element.name] !== '')) element.filterconfig.value = pQuery[element.name]
            }
            if (element.filterconfig.filtertype === 'datetimeinterval') {
              element.filterconfig.value = {}
              if (pQuery[element.name + 'i']) element.filterconfig.value.valuei = pQuery[element.name + 'i']
              if (pQuery[element.name + 'f']) element.filterconfig.value.valuef = pQuery[element.name + 'f']
              if (Object.keys(element.filterconfig.value).length === 0) element.filterconfig.value = null
            }
            if (element.filterconfig.filtertype === 'floatinterval') {
              element.filterconfig.value = {}
              if (pQuery[element.name + 'i']) element.filterconfig.value.valuei = pQuery[element.name + 'i']
              if (pQuery[element.name + 'f']) element.filterconfig.value.valuef = pQuery[element.name + 'f']
              if (Object.keys(element.filterconfig.value).length === 0) element.filterconfig.value = null
            }
          }
        }
      }
    },
    actInputFilter () {
      var app = this
      if (app.loading) return
      app.refreshData(null)
    },
    async showNotaPDF (pChave) {
      var app = this
      var nfe = new NFe()
      var ret = await nfe.dialogShow(app, pChave)
      if (!ret.ok) {
        if (ret.msg ? ret.msg !== '' : false) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {})
        }
      }
    },
    async refreshData (props) {
      var app = this
      app.loading = true
      app.dataset.readPropsTable(props)
      app.dataset.query = {
        perpage: app.dataset.pagination.rowsPerPage,
        page: app.dataset.pagination.page
      }
      if ((app.filter) && (app.filter !== '')) app.dataset.query.filter = app.filter

      for (let index = 0; index < app.columns.length; index++) {
        const element = app.columns[index]
        if (element.filterconfig) {
          if (element.filterconfig.filtertype2 !== '') {
            if (element.filterconfig.datatype2 === JSON) {
              if (element.filterconfig.value2) app.dataset.query[element.name + '2'] = JSON.stringify(element.filterconfig.value2)
            } else {
              if (element.filterconfig.value2) app.dataset.query[element.name + '2'] = element.filterconfig.value2.join(',')
            }
          }
          if (element.filterconfig.filtertype === 'text') {
            if ((element.filterconfig.value) && (element.filterconfig.value !== '')) {
              app.dataset.query[element.name] = element.filterconfig.value
            }
          }
          if (element.filterconfig.filtertype === 'datetimeinterval') {
            if (element.filterconfig.value) {
              if (element.filterconfig.value.valuei) app.dataset.query[element.name + 'i'] = Vue.prototype.$helpers.strDateToFormated(element.filterconfig.value.valuei, 'YYYY/MM/DD', 'YYYY-MM-DD')
              if (element.filterconfig.value.valuef) app.dataset.query[element.name + 'f'] = Vue.prototype.$helpers.strDateToFormated(element.filterconfig.value.valuef, 'YYYY/MM/DD', 'YYYY-MM-DD')
            }
          }
          if (element.filterconfig.filtertype === 'floatinterval') {
            if (element.filterconfig.value) {
              if (element.filterconfig.value.valuei) app.dataset.query[element.name + 'i'] = element.filterconfig.value.valuei
              if (element.filterconfig.value.valuef) app.dataset.query[element.name + 'f'] = element.filterconfig.value.valuef
            }
          }
        }
      }

      var ret = await app.dataset.fetch()
      if (ret.ok) {
        app.rows = ret.data
        // atualiza url
        if (app.dataset.pagination.page !== null && app.dataset.pagination.page > 1) app.dataset.query.page = app.dataset.pagination.page
        if ((app.dataset.pagination.rowsPerPage !== null) && (app.dataset.pagination.rowsPerPage > 0) && (parseInt(app.dataset.pagination.rowsPerPage) !== 20)) app.dataset.query.pagesize = app.dataset.pagination.rowsPerPage
        app.dataset.query.t = new Date().getTime()
        try {
          app.$router.replace({ name: app.$route.name, query: app.dataset.query, replace: true, append: false })
        } catch (error) {
          console.error(error)
        }
      } else {
        if (ret.msg ? ret.msg !== '' : false) {
          var a = app.$helpers.showDialog(ret)
          await a.then(function () {})
        }
      }
      app.loading = false
    }
  }
}
</script>
