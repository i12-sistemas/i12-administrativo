<template>
<div>
  <div class="fit row wrap justify-start items-start content-start q-">
    <div class="col-grow">
      <q-input outlined dense debounce="300" v-model="filter" placeholder="Procurar" ref="txtfind">
        <template v-slot:append>
          <q-icon name="clear" @click="filter=''" v-if="filter != ''" />
          <q-icon name="search" />
        </template>
      </q-input>
    </div>
    <div class="col-2 q-ml-sm" >
      <q-btn color="primary" v-if="!$q.platform.is.mobile" :label="(labelbtn ? (labelbtn === '' ? 'Selecionar' : labelbtn) : 'Selecionar')" no-wrap
        @click="actEnviaSelecionados" class="full-width" unelevated :disable="selected_cidades ? (selected_cidades.length === 0) : true"/>

      <q-btn color="primary" v-if="$q.platform.is.mobile"
        icon="add" @click="actEnviaSelecionados" class="full-width" unelevated :disable="selected_cidades ? (selected_cidades.length === 0) : true"/>
    </div>
    <div class="col-1 q-ml-sm" v-if="showclose" >
      <q-btn color="black" icon="clear" flat no-wrap @click="actClose" class="full-width" unelevated />
    </div>
  </div>
  <q-separator spaced  />
  <div class="col-sm-12">
    <q-table :data="rows" :columns="columns" dense flat
      :loading="loading"
        :pagination.sync="cidades.pagination"
        row-key="id"
        @request="refreshData"
        :filter="filter"
        selection="multiple"
        :selected.sync="selected_cidades"
        >
        <template v-slot:body-cell-id="props">
          <q-td :props="props">
            <q-btn flat :dense="!$q.platform.is.mobile" v-if="!checkifadded(props.row)" icon="add" round @click="actEnviaOne(props.row)" :size="$q.platform.is.mobile ? '12px' : '8px'" >
              <q-tooltip>Adicionar {{props.row.cidade + ' / '  + props.row.uf}} </q-tooltip>
            </q-btn>
            <q-avatar size="15px"  color="accent" text-color="white" icon="check" v-if="checkifadded(props.row)" />
          </q-td>
        </template>
        <template v-slot:body-cell-cidade="props">
          <q-td :props="props" :class="checkifadded(props.row) ? 'text-accent' : ''">
            <div>
                {{props.row.cidade}}
                <q-tooltip >
                  <div>{{props.row.cidade}}</div>
                </q-tooltip>
            </div>
          </q-td>
        </template>
        <template v-slot:body-cell-regiao="props">
          <q-td :props="props">
            <div v-if="props.row.regiao ? props.row.regiao.id > 0 : false">{{props.row.regiao.regiao}}</div>
            <div v-if="!(props.row.regiao ? props.row.regiao.id > 0 : false)">-</div>
          </q-td>
        </template>
        <template v-slot:body-cell-estado="props">
          <q-td :props="props" :class="checkifadded(props.row) ? 'text-accent' : ''">
              {{props.row.estado}}
          </q-td>
        </template>
        <template v-slot:body-cell-codigoibge="props">
          <q-td :props="props" :class="checkifadded(props.row) ? 'text-accent' : ''">
              {{props.row.codigoibge}}
          </q-td>
        </template>
    </q-table>
  </div>
</div>
</template>

<script>
import Cidades from 'src/mvc/collections/cidades.js'
export default {
  components: {
  },
  props: [
    'value', 'listaatual', 'outlined', 'labelbtn', 'showclose', 'allowregiaoid'
  ],
  data () {
    var cidades = new Cidades()
    return {
      cidades,
      options: null,
      showing: false,
      loading: false,
      filter: '',
      selected_cidades: [],
      columns: [
        { name: 'id', style: 'width: 30px', align: 'center', label: '', field: 'id', sortable: false },
        { name: 'cidade', align: 'left', label: 'Cidade', field: 'cidade', sortable: false },
        { name: 'estado', style: 'width: 100px', align: 'left', label: 'Estado', field: 'estado' },
        { name: 'codigoibge', style: 'width: 50px', align: 'center', label: 'Cód. IBGE', field: 'codigo_ibge' },
        { name: 'regiao', style: 'max-width: 150px', align: 'left', label: 'Região', field: row => row.regiao }
      ],
      rows: []
    }
  },
  async mounted () {
    var app = this
    app.actOnFocus()
    document.onkeydown = function (e) {
      if (e.key === 'Escape') {
        app.actClose()
      }
    }
  },
  methods: {
    checkifadded (pCidade) {
      return (this.listaatual ? this.listaatual.indexOf(pCidade.id) >= 0 : false) || (this.allowregiaoid ? (pCidade.regiao ? (pCidade.regiao.id > 0) : false) : false)
    },
    actOnFocus () {
      this.$nextTick(() => {
        this.$refs.txtfind.$el.focus()
      })
    },
    actEnviaOne (pCidadeRow) {
      var app = this
      if (!pCidadeRow) return
      app.sendSelected([pCidadeRow])
    },
    actEnviaSelecionados () {
      var app = this
      if (!app.selected_cidades) return
      if (!(app.selected_cidades.length > 0)) return
      app.sendSelected(app.selected_cidades)
    },
    actClose () {
      this.$emit('close')
    },
    sendSelected: async function (itens) {
      var app = this
      app.$emit('selected', itens)
    },
    async refreshData (props) {
      var app = this
      app.loading = true
      app.msgError = ''
      app.cidades.readPropsTable(props)
      var ret = await app.cidades.fetch()
      if (ret.ok) {
        app.rows = app.cidades.itens
      } else {
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {
        })
      }
      app.loading = false
    }
  }
}
</script>
