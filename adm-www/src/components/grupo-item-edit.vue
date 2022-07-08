<template>
  <div>
    <q-card class="full-width" >
      <q-separator class="bg-primary" style="height: 2px"/>
      <q-toolbar>
        <q-toolbar-title caption>Edição do item</q-toolbar-title>
        <q-btn flat round dense icon="close" v-close-popup />
      </q-toolbar>
      <q-card-section class="row items-center no-wrap">
        <q-form class="q-gutter-md full-width" >
          <q-input filled v-model="grupoedit.titulo" label="Título" maxlength="20"  class="full-width" v-if="grupoedit.tipo == 'p'"/>
          <q-input filled  v-model="grupoedit.subtitulo" label="Dica - Subtítulo" class="full-width" v-if="grupoedit.tipo == 'p'" />
          <div class="row">
              <div class="col">
                <q-btn unelevated icon="fa fa-palette" label="Colorir botão" :style="'color: ' + grupoedit.fontcolor + '; background: ' + grupoedit.bgcolor" @click="editcolor = true" />
              </div>
          </div>
        </q-form>
      </q-card-section>
      <q-separator />
      <q-card-actions class="full-width">
          <div class="row full-width">
            <div class="col-1">
              <q-btn flat round dense icon="delete" v-close-popup color="red" @click="actDelete" />
            </div>
            <div class="col-5">
              <q-btn flat round dense icon="close" v-close-popup label="Cancelar" class="full-width"/>
            </div>
            <div class="col-6">
              <q-btn label="Confirmar" color="primary" class="full-width" @click="actConfirmar"/>
            </div>
          </div>
      </q-card-actions>
    </q-card>
    <q-dialog v-model="editcolor" maximized>
      <q-card class="my-card">
        <q-toolbar class="bg-primary">
          <q-tabs v-model="tab" indicator-color="primary" active-color="white"  active-bg-color="blue-7" class="text-white" align="justify" >
            <q-tab name="bg" icon="fa fa-pen-square" label="cor do fundo" />
            <q-tab name="fonte" icon="fa fa-font" label="cor da fonte" />
          </q-tabs>
          <q-space />
          <q-btn flat round dense icon="close" v-close-popup text-color="white" />
        </q-toolbar>
      <q-card-section class="q-pa-xs">
        <div class="text-body">
          <q-tab-panels v-model="tab" animated>
          <q-tab-panel name="fonte">
            <div class="q-pa-lg" >
              <q-color v-model="grupoedit.fontcolor" format-model="hex" no-header default-view="palette" />
            </div>
          </q-tab-panel>
          <q-tab-panel name="bg">
            <div class="q-pa-lg">
            <q-color v-model="grupoedit.bgcolor" format-model="hex" no-header default-view="palette"/>
            </div>
          </q-tab-panel>
        </q-tab-panels>
        </div>
      </q-card-section>
        <q-separator />
        <q-card-actions align="center">
          <div class="fit row wrap justify-evenly items-start content-start text-black q-pa-xs">
            <div class="col-4 q-col-gutter q-pa-xs text-center" v-for="n in 6" :key="n">
                <grupoitembtn :itembutton="grupoexemplo"  v-if="n !==2" />
                <grupoitembtn :itembutton="grupoedit"  v-if="n ==2" />
            </div>
        </div>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import GrupoItem from 'src/mvc/models/grupoitem.js'
import grupoitembtn from 'src/components/grupo-item-btn'
export default {
  props: ['item'],
  components: {
    grupoitembtn
  },
  data () {
    let grupoedit = new GrupoItem()
    let grupoexemplo = new GrupoItem()
    return {
      grupoedit,
      tab: 'bg',
      editcolor: false,
      grupoexemplo
    }
  },
  mounted () {
    var app = this
    app.grupoedit.cloneFrom(app.item.item)
    app.grupoexemplo.titulo = 'Exemplo botao'
    app.grupoexemplo.bgcolor = '#fafafa'
    app.grupoexemplo.fontcolor = '#000000'
  },
  methods: {
    actDelete () {
      this.$emit('deleteItem', this.item.idx, this.grupoedit)
    },
    actConfirmar () {
      this.$emit('saveItem', this.item.idx, this.grupoedit)
    },
    actDefaultColor () {
      this.grupoedit.bgcolor = '#fafafa'
      this.grupoedit.fontcolor = '#000000'
    },
    async actAddItemFav (pItemButton) {
      this.$emit('onClick', this.item.idx, this.itembutton)
    }
  }
}
</script>
