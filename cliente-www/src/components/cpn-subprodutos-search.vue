<template>
<div>
  <div class="row">
    <div class="full-width">
      <q-toolbar>
        <q-input v-model="strSearch" ref="txtsearch" class="full-width"  placeholder="Descrição do subgrupo"
          @input="actFind" debounce="700" :loading="finding"
          :hint="((grupos.itens) && (grupos.total) ? (grupos.itens.length > 0 && grupos.itens.length < grupos.total) : false) ? 'Mostrando ' + grupos.itens.length + ' de ' + grupos.total : ''">
          <template v-slot:append>
            <q-icon v-if="strSearch === ''" name="search"  />
            <q-icon v-else name="clear" class="cursor-pointer" @click="resetSearch" />
          </template>
        </q-input>
      </q-toolbar>
    </div>

    <div class="full-width">
      <div v-if="(datalist ? datalist.length == 0 : true) && (strSearch != '') && !finding">
        <div>
          <div class="text-center q-pa-md"><q-icon name="sentiment_dissatisfied" size="50px" /></div>
          <div class="text-center q-pa-md">Nenhum subgrupo encontrado com a referência de busca!</div>
          <div class="text-center q-pa-md">
            <q-btn label="Limpar e pesquisar novamente" rounded outline  @click="resetSearch" icon="clear" />
          </div>
        </div>
      </div>
      <q-list separator v-if="datalist ? datalist : false">
        <q-item v-for="grupo in datalist" :key="grupo.id" class="q-my-sm" clickable v-ripple
          @click="addOrRemoveItem((listadestaque ? listadestaque.indexOf(grupo.id) == -1 : false), grupo)"
          :class="(listadestaque ? listadestaque.indexOf(grupo.id) == -1 : true) ? '' : 'bg-grey-2'">
          <q-item-section>
            <q-item-label class="text-subtitle1">{{ grupo.titulo }}</q-item-label>
          </q-item-section>
          <q-item-section top side>
            <div class="text-grey-8">
              <q-avatar round color="grey-3" text-color="blue" icon="add" v-if="listadestaque ? listadestaque.indexOf(grupo.id) == -1 : true" >
              </q-avatar>
              <q-avatar  icon="delete_forever" unelevated round color="white" text-color="red" v-if="(listadestaque ? listadestaque.indexOf(grupo.id) >=0 : false) && (shownumordem ? !shownumordem : true)">
              </q-avatar>
              <q-avatar  icon="delete_forever" unelevated round color="white" text-color="red" v-if="(listadestaque ? listadestaque.indexOf(grupo.id) >=0 : false) && (shownumordem ? !shownumordem : true)">
              </q-avatar>
              <q-avatar  unelevated round color="white" text-color="red" v-if="(listadestaque ? listadestaque.indexOf(grupo.id) >=0 : false) && (shownumordem ? shownumordem : true)">
                {{ (listadestaque.indexOf(grupo.id) + 1 ) + 'º'}}
              </q-avatar>
            </div>
          </q-item-section>
        </q-item>
      </q-list>
    </div>
  </div>
</div>
</template>

<script>
import Grupos from 'src/mvc/collections/grupos.js'
export default {
  name: 'subgruposconsulta',
  props: ['listadestaque', 'shownumordem'],
  components: {
  },
  data () {
    let grupos = new Grupos()
    return {
      grupos,
      strSearch: '',
      datalist: null,
      finding: false
    }
  },
  async mounted () {
    // var app = this
    this.resetSearch()
    this.refreshData('', true)
    // this.produtos = []
    // app.$dbtemp.produtos.forEach(function (element, key) {
    //   app.produtos.push(element)
    // })
  },
  methods: {
    resetSearch () {
      this.strSearch = ''
      this.$refs.txtsearch.$el.focus()
    },
    async addOrRemoveItem (pAdd, pGrupo) {
      if (pAdd) {
        this.addItem(pGrupo)
      } else {
        this.removeItem(pGrupo)
      }
    },
    async addItem (pGrupo) {
      this.$emit('grupoadicionado', pGrupo)
    },
    async removeItem (pGrupo) {
      this.$emit('gruporemovido', pGrupo)
    },
    async actFind () {
      var app = this
      app.finding = true
      await app.refreshData(app.strSearch, true)
      app.finding = false
    },
    async refreshData (pStr, pRestart) {
      var app = this
      app.grupos.limpardados()
      app.grupos.somentemeus = 1
      app.grupos.uuid = app.$store.state.dispositivo.uuid
      app.grupos.subgrupo = 1
      var ret = await app.grupos.fetch(pStr)
      if ((!ret.ok) && (ret.msg !== '')) app.msgError = ret.msg
      if (ret.ok) {
        if (pRestart) app.datalist = app.grupos.itens
      }
    }
  }
}
</script>
