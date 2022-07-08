<template>
<div>
  <div class="fit row wrap" v-if="label != ''" >
    <div class="col-12">
      <strong>{{label}}</strong>
    <q-separator spaced />
    </div>
  </div>
  <div class="fit row wrap q-col-gutter-x-md q-col-gutter-y-md" >
    <div class="col-md-3 col-xs-6">
      <q-input outlined  label="CEP" stack-label  type="tel"  :dense="dense" v-model="endereco.cep" :loading="loadingcep"
        ref="txtcep" :mask="'##.###-###'" unmasked-value :readonly="disabled" @input="sendEmit"
        @focus="cepfocused=true" @blur="cepfocused=false">
        <template v-slot:append v-if="!disabled">
          <q-btn round dense size="10px" flat icon="clear" @click="endereco.cep=''" v-if="!loadingcep && (endereco.cep ? endereco.cep.length > 0 : false)" />
          <q-btn round dense size="10px" flat icon="search" @click="actConsultaCEP" v-if="!loadingcep && (endereco.cep ? endereco.cep.length == 8 : false)" />
        </template>
      </q-input>
    </div>
    <div class="col-md-9 col-xs-6 text-caption">
      <div class="full-width row wrap justify-end items-end content-end q-gutter-x-sm">
        <span v-if="!disabled">
        <q-btn  v-for="(btn, key) in buttons" :key="key" :text-color="btn.textcolor" :color="btn.color" :icon="btn.icon" size="12px"
          :unelevated="btn.unelevated" :round="btn.round"  :label="$q.platform.is.mobile ? '' : btn.label"
          @click="actBtnEmit(key)">
          <q-tooltip v-if="(btn.tooltip ? btn.tooltip !== '' : false)">
            <div>{{btn.tooltip}}</div>
          </q-tooltip>
        </q-btn>
        </span>
        <q-btn color="accent" icon="map" size="12px"  unelevated round @click="$helpers.mapslink(endereco.enderecocompleto)"  v-if="endereco.enderecocompleto !== ''" >
          <q-tooltip>
            <div>Ver no Google Mapas</div>
            <div>{{endereco.enderecocompleto}}</div>
          </q-tooltip>
        </q-btn>
        <q-btn color="grey-2" text-color="black" icon="clear" size="12px" unelevated round @click="actLimpar" v-if="!disabled"  >
          <q-tooltip>
            <div>Limpar endereço atual</div>
          </q-tooltip>
        </q-btn>
      </div>
    </div>
    <div class="col-md-3 col-xs-12">
      <selectlogradouro outlined  label="Logradouro" stack-label :dense="dense" v-model="endereco.logradouro" :readonly="disabled" @input="sendEmit"/>
    </div>
    <div class="col-md-7 col-xs-12">
      <q-input outlined input-class="text-uppercase" label="Endereço" stack-label :maxlength="maxendereco ? maxendereco : 200" :dense="dense" v-model="endereco.endereco" :readonly="disabled" @input="sendEmit" />
    </div>
    <div class="col-md-2 col-xs-12">
      <q-input outlined input-class="text-uppercase"  label="Número" stack-label :maxlength="maxnumero ? maxnumero : 10" :dense="dense" v-model="endereco.numero" :readonly="disabled" @input="sendEmit"/>
    </div>
    <div class="col-md-4 col-xs-12">
      <q-input outlined input-class="text-uppercase"  label="Bairro" stack-label :maxlength="maxbairro ? maxbairro : 70" :dense="dense" v-model="endereco.bairro" :readonly="disabled" @input="sendEmit"/>
    </div>
    <div class="col-md-4 col-xs-12">
      <q-input outlined input-class="text-uppercase"  label="Complemento" stack-label :maxlength="maxcomplemento ? maxcomplemento : 70" :dense="dense" v-model="endereco.complemento" :readonly="disabled" @input="sendEmit"/>
    </div>
    <div class="col-md-4 col-xs-12">
      <selectcidade outlined label="Cidade / UF" nullabled :dense="dense" v-model="endereco.cidade" :loading="loadingcep" :readonly="disabled" @updated="sendEmit" />
    </div>
  </div>
</div>
</template>
<script>
import Vue from 'vue'
import Cidade from 'src/mvc/models/cidade.js'
import selectcidade from 'src/components/cnp-select-cidade'
import selectlogradouro from 'src/components/cnp-select-logradouro'
import Endereco from 'src/mvc/models/endereco.js'
export default {
  name: 'cpn-enderecoedit',
  components: {
    selectcidade,
    selectlogradouro
  },
  props: {
    value: {
      type: Object,
      default: function () {
        return { message: 'hello' }
      }
    },
    buttons: {
      type: Array,
      default: function () {
        return []
      }
    },
    dense: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    enderecoupdated: {
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
  watch: {
    enderecoupdated: async function (val) {
      var app = this
      if (this.loading) return
      if (!val) return
      app.loadingcep = true
      app.endereco = new Endereco(app.value)
      Vue.nextTick(function () {
        app.loadingcep = false
      })
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
    },
    async actConsultaCEP () {
      var app = this
      app.loadingcep = true
      var ret = await app.endereco.consultaCEP(app.endereco.cep)
      if (ret.ok) {
        var data = ret.data
        if (data.logradouro !== '') app.endereco.logradouro = data.logradouro
        if (data.endereco !== '') app.endereco.endereco = data.endereco
        if (data.complemento !== '') app.endereco.complemento = data.complemento.toUpperCase()
        if (data.bairro !== '') app.endereco.bairro = data.bairro.toUpperCase()
        if (data.ibge) {
          if (!app.endereco.cidade) app.endereco.cidade = new Cidade()
          await app.endereco.cidade.findByCodigoIBGE(data.ibge)
        }
      } else {
        app.loadingcep = false
        app.$helpers.showDialog(ret)
      }
      app.loadingcep = false
      app.sendEmit()
    },
    async sendEmit () {
      var app = this
      if (this.loading) return
      // if (this.intcidade) await this.endereco.cidade.cloneFrom(this.intcidade)
      app.$emit('input', app.endereco)
      app.$emit('updated')
    },
    actBtnEmit (pKey) {
      this.$emit('clickbtn', pKey)
    },
    async actLimpar () {
      var app = this
      app.loadingcep = true
      await app.endereco.limpardados()
      Vue.nextTick(function () {
        app.loadingcep = false
      })
      this.sendEmit()
    }
  }
}
</script>
