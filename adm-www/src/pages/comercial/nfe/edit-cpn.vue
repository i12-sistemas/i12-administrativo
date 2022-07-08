<template>
<div>
  <q-card :bordered="mode == 'integrado'" flat :class="$q.platform.is.mobile ? 'no-padding' : ''">
    <q-card-section horizontal v-if="permitecomercialnfeconsultaporchave ? !permitecomercialnfeconsultaporchave.ok : true" >
      <div class="full-width">
        <div class="q-pt-lg text-center">
          <p>
            <q-avatar size="100px" font-size="52px" text-color="grey-9" color="grey-3" icon="security" />
          </p>
          <div class="text-faded q-pa-lg text-subtitle2">
            <div class="q-pa-md  text-h6"><b>Sem permissão de acesso!</b></div>
            <div class="q-pa-md">{{permitecomercialnfeconsultaporchave ? permitecomercialnfeconsultaporchave.msg : ''}}</div>
          </div>
        </div>
      </div>
        <div class="text-h6 q-py-sm q-px-md" v-if="loading"></div>
    </q-card-section>
    <q-card-section horizontal v-if="(mode == 'integrado') && (permitecomercialnfeconsultaporchave ? permitecomercialnfeconsultaporchave.ok : false)" >
        <div class="text-h6 q-py-sm q-px-md" v-if="!loading">Consulta NF-e</div>
        <div class="text-h6 q-py-sm q-px-md" v-if="loading">Consultando NF-e...</div>
        <q-space />
        <q-btn icon="open_in_new" flat label="Abrir em outra aba" @click="$helpers.showPrint(nfe.danfe.url)" v-if="nfe.danfe ? nfe.danfe.url !== '' : false" />
    </q-card-section>
    <q-separator class="q-pa-0 q-ma-0" v-if="!loading && (mode == 'integrado') && (permitecomercialnfeconsultaporchave ? permitecomercialnfeconsultaporchave.ok : false)" />
    <q-card-section :class="$q.platform.is.mobile ? 'no-padding' : ''" v-if="(permitecomercialnfeconsultaporchave ? permitecomercialnfeconsultaporchave.ok : false)">
      <div class="fit row wrap">
        <div class="col-12">
          <q-input outlined v-model="chaveinput" label="Chave*" stack-label maxlength="44" counter :dense="!$q.platform.is.mobile" autofocus @keyup.enter="actSearch"
              lazy-rules :rules="[ val => val && (val.length == 44 )|| 'Obrigatório informar a chave. 44 números']"
              :loading="loading"
            >
            <template v-slot:append>
              <q-btn round dense flat icon="search" @click="actSearch"  />
            </template>
          </q-input>
        </div>
      </div>
    </q-card-section>
    <q-card-section v-if="loading" :class="$q.platform.is.mobile ? 'no-padding' : 'q-pa-lg'">
        <q-linear-progress indeterminate />
        <div class="text-center full-width q-pa-md text-primary">Consultando nota</div>
    </q-card-section>
    <q-card-section  horizontal v-if="!loading && (msgError ? msgError != '' : false)" class="q-pa-md" >
      <q-banner class="bg-red text-white full-width">
        {{msgError}}
        <template v-slot:action>
          <q-btn flat color="white" label="Limpar consulta" @click="actReset" />
        </template>
      </q-banner>
    </q-card-section>
    <q-card-section :class="$q.platform.is.mobile ? 'no-padding' : ''" v-if="(permitecomercialnfeconsultaporchave ? permitecomercialnfeconsultaporchave.ok : false)" >
        <div class="fit row wrap full-height">
          <div class="col-12" v-if="!loading && (nfe.danfe ? nfe.danfe.url != '' : false)">
            <div class="text-caption text-right">
              Caso o arquivo PDF não tenha carregado abaixo, clique em "Abrir em outra aba".
            </div>
            <iframe :src="nfe.danfe.url" class="full-width" style="height: 60vh" />
          </div>
        </div>
    </q-card-section>
  </q-card>
</div>
</template>

<script>
import NFe from 'src/mvc/models/nfe.js'

export default {
  name: 'nfe.view.cpn',
  components: {
  },
  props: {
    mode: {
      type: String,
      default: function () {
        return 'integrado' // ou dialog
      }
    },
    chave: {
      type: String,
      default: function () {
        return null
      }
    }
  },
  data () {
    var nfe = new NFe()
    return {
      nfe,
      chaveinput: null,
      loading: false,
      msgError: '',
      permitecomercialnfeconsultaporchave: null
    }
  },
  async mounted () {
    var app = this
    app.permitecomercialnfeconsultaporchave = app.$helpers.permite('comercial.nfe.consultaporchave')
    await this.init()
  },

  methods: {
    async init () {
      var app = this
      app.loading = true
      app.chaveinput = app.chave
      await app.refreshData()
      app.loading = false
    },
    async actSearch () {
      var app = this
      app.refreshData()
    },
    actReset () {
      this.chaveinput = null
      this.msgError = null
      this.loading = false
    },
    actClose () {
      this.$emit('close', false)
    },
    async refreshData () {
      var app = this
      if (!app.permitecomercialnfeconsultaporchave) return
      if (!app.permitecomercialnfeconsultaporchave.ok) return
      app.loading = true
      app.msgError = ''
      try {
        if (app.mode === 'integrado') {
          try {
            app.$router.replace({ name: app.$route.name, query: { chave: app.chaveinput ? app.chaveinput : '' }, replace: true, append: false })
          } catch (error) {
            console.error(error)
          }
        }
        if (!app.chaveinput) throw new Error('Nenhuma chave informada')
        if (app.chaveinput.length !== 44) throw new Error('Chave inválida. Deve ter 44 números')
      } catch (error) {
        app.msgError = error.message
        app.loading = false
        return
      }
      var ret = await app.nfe.getpdf(app.chaveinput)
      if (ret.ok) {
      } else {
        app.msgError = ret.msg
        app.loading = false
      }
      app.loading = false
    }
  }
}
</script>
