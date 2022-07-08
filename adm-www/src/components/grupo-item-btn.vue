<template>
<div>
  <btnpri :itembutton="itembutton" :idx="idx" @onClick="actAddItemFav" ></btnpri>
  <q-dialog v-model="showdialog" transition-show="rotate" transition-hide="rotate" position="top">
    <q-card style="border-radius: 0 0 10px 10px" >
      <q-toolbar :style="'color: ' + itembutton.fontcolor + '; background: ' + itembutton.bgcolor" >
        <q-toolbar-title>{{subgrupoSelected.titulo}}</q-toolbar-title>
        <q-btn icon="close" flat round dense v-close-popup />
      </q-toolbar>
      <q-card-section class="q-pt-none">
        <div class="fit row wrap justify-evenly items-start content-start text-black q-pa-xs" id="items">
          <div class="col-4 q-col-gutter q-pa-xs text-center" v-for="(btnsubgrupo, keyi) in subgrupoSelected.itens" :key="keyi" :data-id="keyi">
              <btn :itembutton="btnsubgrupo" :idx="keyi" @onClick="actClickSubGrupo" ></btn>
          </div>
        </div>
      </q-card-section>

      <q-card-actions align="between" class="bg-grey-2">
        <q-btn unelevated label="Finalizar" color="positive" v-close-popup />
        <q-btn flat label="Fechar" icon="clear" color="black" v-close-popup />
      </q-card-actions>
    </q-card>
  </q-dialog>
</div>
</template>

<script>
import Grupo from 'src/mvc/models/grupo.js'
import btnitem from './grupo-item-btn-child'
export default {
  name: 'grupo-item-btn',
  props: ['itembutton', 'idx', 'disabledopensubgrupo'],
  components: {
    'btn': btnitem,
    'btnpri': btnitem
  },
  data () {
    let subgrupoSelected = new Grupo()
    return {
      showdialog: false,
      subgrupoSelected
    }
  },
  mounted () {
  },
  methods: {
    async actClickSubGrupo (item) {
      var app = this
      app.actAddItemFav(item)
    },
    async actAddItemFav (pItemButton) {
      var app = this
      if (pItemButton.tipo === 'p') {
        this.$emit('onClick', this.idx, pItemButton)
      } else if (pItemButton.tipo === 'g') {
        if (app.disabledopensubgrupo) {
          this.$emit('onClick', this.idx, pItemButton)
        } else {
          this.showdialog = true
          var ret = await app.subgrupoSelected.find(pItemButton.idgrupo)
          if (!ret.ok) app.$helpers.showDialog(ret)
        }
      }
    }
  }
}
</script>
