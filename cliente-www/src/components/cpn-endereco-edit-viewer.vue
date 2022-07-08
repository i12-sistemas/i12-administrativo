<template>
<div>
  <div class="fit row wrap" v-if="label != ''" >
    <div class="col-12">
      <strong>{{label}}</strong>
    <q-separator spaced />
    </div>
  </div>
  <div class="fit row wrap q-col-gutter-x-md q-col-gutter-y-md" >
    <div class="col-md-2 col-xs-12">
      <q-field label="Logradouro" stack-label outlined :dense="!$q.platform.is.mobile" >
        <template v-slot:control>
          <div class="self-center full-width no-outline" tabindex="0">{{endereco.logradouro}}</div>
        </template>
      </q-field>
    </div>
    <div class="col-md-6 col-xs-12">
      <q-field label="Endereço" stack-label outlined :dense="!$q.platform.is.mobile" >
        <template v-slot:control>
          <div class="self-center full-width no-outline" tabindex="0">{{endereco.endereco}}</div>
        </template>
        <template v-slot:append>
          <q-btn color="grey" icon="map" size="sm" unelevated round @click="$helpers.mapslink(endereco.enderecocompleto)"  v-if="endereco.enderecocompleto !== ''" >
            <q-tooltip>
              <div>Ver no Google Mapas</div>
              <div>{{endereco.enderecocompleto}}</div>
            </q-tooltip>
        </q-btn>
        </template>
      </q-field>
    </div>
    <div class="col-md-2 col-xs-12">
      <q-field label="Número" stack-label outlined :dense="!$q.platform.is.mobile" >
        <template v-slot:control>
          <div class="self-center full-width no-outline" tabindex="0">{{endereco.numero}}</div>
        </template>
      </q-field>
    </div>
    <div class="col-md-2 col-xs-6">
      <q-field label="CEP" stack-label outlined :dense="!$q.platform.is.mobile" >
        <template v-slot:control>
          <div class="self-center full-width no-outline" tabindex="0">{{ $helpers.mascaraCEP(endereco.cep) }}</div>
        </template>
      </q-field>
    </div>
    <div class="col-md-4 col-xs-12">
      <q-field label="Bairro" stack-label outlined :dense="!$q.platform.is.mobile" >
        <template v-slot:control>
          <div class="self-center full-width no-outline" tabindex="0">{{endereco.bairro}}</div>
        </template>
      </q-field>
    </div>
    <div class="col-md-4 col-xs-12">
      <q-field label="Complemento" stack-label outlined :dense="!$q.platform.is.mobile" >
        <template v-slot:control>
          <div class="self-center full-width no-outline" tabindex="0">{{endereco.complemento}}</div>
        </template>
      </q-field>
    </div>
    <div class="col-md-4 col-xs-12">
      <q-field label="Cidade / UF" stack-label outlined :dense="!$q.platform.is.mobile" >
        <template v-slot:control>
          <div class="self-center full-width no-outline" tabindex="0">{{endereco.cidade ? endereco.cidade.cidade + ' / ' + endereco.cidade.uf : '-'}}</div>
        </template>
      </q-field>
    </div>
  </div>
</div>
</template>
<script>
import Vue from 'vue'
import Endereco from 'src/mvc/models/endereco.js'
export default {
  name: 'cpn-enderecoedit',
  components: {
  },
  props: {
    value: {
      type: Object,
      default: function () {
        return { message: 'hello' }
      }
    },
    dense: {
      type: Boolean,
      default: false
    },
    label: { type: String, default: 'Endereço' },
    maxbairro: { type: Number, default: 100 },
    maxendereco: { type: Number, default: 100 },
    maxnumero: { type: Number, default: 100 },
    maxcomplemento: { type: Number, default: 100 }
  },
  data () {
    let endereco = new Endereco()
    return {
      endereco,
      modelinterno: null,
      loading: true,
      loadingcep: true,
      cepfocused: false
    }
  },
  async mounted () {
    var app = this
    app.loading = true
    app.loadingcep = true
    await app.actReload()
    document.onkeydown = function (e) {
      if ((e.keyCode === 13) && (app.cepfocused)) {
        app.actConsultaCEP()
        e.preventDefault()
      }
    }
    Vue.nextTick(function () {
      app.loadingcep = false
      app.loading = false
    })
  },
  methods: {
    async refresh () {
      this.loading = true
      this.loadingcep = true
      await this.actReload()
      this.loading = false
      this.loadingcep = false
    },
    async actReload () {
      var app = this
      app.endereco = new Endereco(app.value)
    }
  }
}
</script>
