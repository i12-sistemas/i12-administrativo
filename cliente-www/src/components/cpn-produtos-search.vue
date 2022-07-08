<template>
<div>
  <div class="row">
    <div class="full-width">
      <q-toolbar>
        <q-input v-model="strSearch" ref="txtsearch" class="full-width"  placeholder="Produtos"
          @input="actFind" debounce="700" :loading="findproduto"
          :hint="((produtos.itens) && (produtos.total) ? (produtos.itens.length > 0 && produtos.itens.length < produtos.total) : false) ? 'Mostrando ' + produtos.itens.length + ' de ' + produtos.total : ''">
          <template v-slot:append>
            <q-icon v-if="strSearch === ''" name="search"  />
            <q-icon v-else name="clear" class="cursor-pointer" @click="resetSearch" />
          </template>
        </q-input>
      </q-toolbar>
    </div>

    <div class="full-width">
      <div v-if="(datalist ? datalist.length == 0 : true) && (strSearch != '') && !findproduto">
        <div>
          <div class="text-center q-pa-md"><q-icon name="sentiment_dissatisfied" size="50px" /></div>
          <div class="text-center q-pa-md">Nenhum produto encontrado com a referência de busca!</div>
          <div class="text-center q-pa-md">
            <q-btn label="Limpar e pesquisar novamente" rounded outline  @click="resetSearch" icon="clear" />
          </div>
        </div>
      </div>
      <q-card v-if="(strSearch == '') && !findproduto" bordered flat class="bg-grey-1">
        <q-card-section>
          <div class="text-center q-pa-md">Informe no mínimo um caracter para realizar a busca de produto.</div>
          <div class="text-center q-pa-md"><strong>Dica: </strong> Use o caracter % como coringa para consultar por partes.</div>
        </q-card-section>
      </q-card>
      <q-list separator v-if="datalist ? datalist : false">
        <q-item v-for="produto in datalist" :key="produto.id" class="q-my-sm" clickable v-ripple
          @click="addOrRemoveItem((listadestaque ? listadestaque.indexOf(produto.codproduto) == -1 : false), produto)"
          :class="(listadestaque ? listadestaque.indexOf(produto.codproduto) == -1 : true) ? '' : 'bg-grey-2'">
          <q-item-section>
            <q-item-label class="text-subtitle1">{{ produto.descricao }}</q-item-label>
            <q-item-label class="text-caption" lines="1">Cód.: {{ produto.codproduto }} | {{ produto.unid }} - {{ $helpers.formatRS(produto.preco) }}</q-item-label>
          </q-item-section>
          <q-item-section top side>
            <div class="text-grey-8">
              <q-avatar round color="grey-3" text-color="blue" icon="add" v-if="listadestaque ? listadestaque.indexOf(produto.codproduto) == -1 : true" >
              </q-avatar>
              <q-avatar  icon="delete_forever" unelevated round color="white" text-color="red" v-if="(listadestaque ? listadestaque.indexOf(produto.codproduto) >=0 : false) && (shownumordem ? !shownumordem : true)">
              </q-avatar>
              <q-avatar  icon="delete_forever" unelevated round color="white" text-color="red" v-if="(listadestaque ? listadestaque.indexOf(produto.codproduto) >=0 : false) && (shownumordem ? !shownumordem : true)">
              </q-avatar>
              <q-avatar  unelevated round color="white" text-color="red" v-if="(listadestaque ? listadestaque.indexOf(produto.codproduto) >=0 : false) && (shownumordem ? shownumordem : true)">
                {{ (listadestaque.indexOf(produto.codproduto) + 1 ) + 'º'}}
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
import Produtos from 'src/mvc/collections/produtos.js'
export default {
  name: 'produtos',
  props: ['listadestaque', 'shownumordem'],
  components: {
  },
  data () {
    let produtos = new Produtos()
    return {
      produtos,
      strSearch: '',
      datalist: null,
      findproduto: false
    }
  },
  async mounted () {
    // var app = this
    this.resetSearch()
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
    async addOrRemoveItem (pAdd, pProduto) {
      if (pAdd) {
        this.addItem(pProduto)
      } else {
        this.removeItem(pProduto)
      }
    },
    async addItem (pProduto) {
      this.$emit('produtoadicionado', pProduto)
    },
    async removeItem (pProduto) {
      this.$emit('produtoremovido', pProduto)
    },
    async actFind () {
      var app = this
      app.findproduto = true
      await app.refreshData(app.strSearch, true)
      app.findproduto = false
    },
    async refreshData (pStr, pRestart) {
      var app = this
      var ret = await app.produtos.fetch(pStr)
      if ((!ret.ok) && (ret.msg !== '')) app.msgError = ret.msg
      if (ret.ok) {
        if (pRestart) app.datalist = app.produtos.itens
      }
    }
  }
}
</script>
