  <template>
<div>
  <q-page class="">
    <div class="row doc-page" :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-pa-lg'" >
      <q-card bordered flat :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-mt-md'" class="full-width">
        <q-card-section horizontal >
          <q-toolbar class="bg-white text-black">
            <q-btn flat round dense icon="settings" />
            <q-toolbar-title>
              {{categoriaselecionada ? categoriaselecionada.label : 'Configuração'}}
            </q-toolbar-title>
            <q-btn flat stretch  icon="more_vert" label="Categorias" :disable="saving"  >
              <q-menu >
                <q-list style="min-width: 100px" dense flat>
                  <q-item clickable v-close-popup v-for="(categ, kcateg) in categorias" :key="kcateg" @click="actSelectCateg(categ)">
                    <q-item-section>{{categ.label}}</q-item-section>
                  </q-item>
                  <q-separator />
                </q-list>
              </q-menu>
            </q-btn>
            <q-separator spaced inset vertical dark />
            <q-btn color="black" icon="check" flat :label="$q.platform.is.mobile ? '' : 'Salvar'" stretch :disable="saving" :loading="saving" v-if="categoriaselecionada && (permiteconfigconfiguracaogeral ? permiteconfigconfiguracaogeral.ok : false)" @click="actSave"/>
            <q-btn color="black" icon="sync" flat stretch  :loading="loading || saving" @click="actRefresh" :disable="loading || saving"  />
          </q-toolbar>
        </q-card-section>
        <q-separator class="q-pa-0 q-ma-0" v-if="!loading" />
        <q-card-section v-if="(permiteconfigconfiguracaogeral ? !permiteconfigconfiguracaogeral.ok : true)" class="text-center text-red" >
          <div>Sem permissão de acesso</div>
          <div v-if="permiteconfigconfiguracaogeral ? permiteconfigconfiguracaogeral.msg !== '' : false">{{permiteconfigconfiguracaogeral.msg ? permiteconfigconfiguracaogeral.msg : ''}}</div>
        </q-card-section>
        <q-card-section v-if="categoriaselecionada && (permiteconfigconfiguracaogeral ? permiteconfigconfiguracaogeral.ok : false)" class="no-padding" >
           <component :is="categoriaselecionada.id" ref="componenteCateg" @saved="actSaved" @saving="actSaving" @loading="actLoading" />
        </q-card-section>
      </q-card>

    </div>
  </q-page>
</div>
</template>

<script>
import configacertoviagem from './categ/acertoviagem'
import configcoleta from './categ/coleta'
import configmanutencaoveiculo from './categ/manutencaoveiculo'
import configdispositivomovel from './categ/dispositivomovel'
// import formedit from './edit-cnp'
export default {
  name: 'acerto.edit',
  components: {
    configacertoviagem,
    configmanutencaoveiculo,
    configdispositivomovel,
    configcoleta
  },
  data () {
    return {
      permiteconfigconfiguracaogeral: null,
      idstart: null,
      showadd: false,
      dataset: null,
      saving: false,
      categorias: [
        { id: 'configcoleta', label: 'Coletas' },
        { id: 'configacertoviagem', label: 'Acerto de viagem' },
        { id: 'configmanutencaoveiculo', label: 'Manutenção de veículo' },
        { id: 'configdispositivomovel', label: 'Dispositivo móvel' }
      ],
      categoriaselecionada: null,
      loading: true
    }
  },
  async mounted () {
    var app = this
    app.permiteconfigconfiguracaogeral = app.$helpers.permite('config.configuracao.geral')
    if (app.$route.query.id) {
      app.idstart = app.$route.query.id
    }
    var start = app.categorias[0]
    if (app.idstart) {
      for (let index = 0; index < app.categorias.length; index++) {
        const categ = app.categorias[index]
        if (categ.id === app.idstart) {
          start = app.categorias[index]
          break
        }
      }
    }
    app.actSelectCateg(start)
  },
  methods: {
    async actSelectCateg (pCateg) {
      var app = this
      if (app.permiteconfigconfiguracaogeral) {
        if (!app.permiteconfigconfiguracaogeral.ok) {
          app.loading = false
          return
        }
      }
      app.categoriaselecionada = pCateg
    },
    async actRefresh () {
      var app = this
      if (app.permiteconfigconfiguracaogeral) {
        if (!app.permiteconfigconfiguracaogeral.ok) {
          app.loading = false
          return
        }
      }
      app.$refs.componenteCateg.refreshdata()
    },
    actSaving (saving) {
      this.saving = saving
    },
    actLoading (loading) {
      this.loading = loading
    },
    async actSaved (ret) {
      var app = this
      if (ret.ok) {
        app.$q.notify({
          message: 'Configuração salva!',
          color: 'positive',
          caption: 'Configuração aplicada com sucesso!',
          actions: [
            { label: 'OK', color: 'white', handler: () => { /* ... */ } }
          ]
        })
      } else {
        app.$helpers.showDialog(ret, ret.warning)
      }
    },
    async actSave () {
      var app = this
      if (app.permiteconfigconfiguracaogeral) {
        if (!app.permiteconfigconfiguracaogeral.ok) {
          app.loading = false
          return
        }
      }
      app.$refs.componenteCateg.savedata()
    }
  }
}
</script>
