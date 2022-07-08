<template>
<div>
  <q-btn @click="actClick(itembutton)" unelevated square  class="full-width" :push="itembutton.tipo == 'g'"
      :style="'color: ' + itembutton.fontcolor + '; background: ' + itembutton.bgcolor" v-if="itembutton.imagetype == ''" style="height: 85px; border-radius: 5px">
    <div>{{itembutton.titulo}}</div>
    <q-tooltip content-class="bg-black" multilined :delay="1000"><p v-html="infotooltip"></p></q-tooltip>
  </q-btn>
  <q-btn @click="actClick(itembutton)" unelevated rounded style="height: 85px; border-radius: 5px" class="full-width"
      :style="'color: ' + itembutton.fontcolor + '; background: ' + itembutton.bgcolor" v-if="itembutton.imagetype == 'fonticon' && itembutton.imagesrc != ''" >
    <q-avatar rounded  size="42px" :icon="itembutton.imagesrc">
    </q-avatar>
    <div>{{itembutton.titulo}}</div>
    <q-tooltip content-class="bg-black" multilined :delay="1000"><p v-html="infotooltip"></p></q-tooltip>
  </q-btn>
  <q-btn @click="actClick(itembutton)" unelevated rounded style="height: 85px; border-radius: 5px" class="full-width"
    v-if="itembutton.imagetype == 'image' && itembutton.imagesrc != ''" :style="'color: ' + itembutton.fontcolor + '; background: ' + itembutton.bgcolor">
    <q-avatar rounded  size="42px">
      <img :src="itembutton.imagesrc">
    </q-avatar>
    <div>{{itembutton.titulo}}</div>
    <q-tooltip content-class="bg-black" multilined :delay="1000"><p v-html="infotooltip"></p></q-tooltip>
  </q-btn>
</div>
</template>

<script>
export default {
  name: 'side-menu-item',
  props: ['itembutton'],
  data () {
    return {
    }
  },
  mounted () {
  },
  methods: {
    async actClick (pItemButton) {
      this.$emit('onClick', this.itembutton)
    }
  },
  computed: {
    infotooltip: function () {
      var app = this
      var s = ''
      if (app.itembutton) {
        if (app.itembutton.subtitulo) s = app.itembutton.subtitulo === '' ? '' : app.itembutton.subtitulo
        if ((app.itembutton.itemproduto) && (app.itembutton.tipo === 'p')) {
          s += (s !== '' ? '<br>' : '') + '<br>Produto'
          s += (s !== '' ? '<br>' : '') + 'Cód.: ' + app.itembutton.itemproduto.codproduto
          s += (s !== '' ? '<br>' : '') + 'Descrição: ' + app.itembutton.itemproduto.descricao
          s += (s !== '' ? '<br>' : '') + 'Unid: ' + app.itembutton.itemproduto.unid
          s += (s !== '' ? '<br>' : '') + 'Preço: ' + app.$helpers.formatRS(app.itembutton.itemproduto.preco)
        }
      }
      return s
    }
  }
}
</script>
