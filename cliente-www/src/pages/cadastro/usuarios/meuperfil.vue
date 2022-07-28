<template>
<div>
  <q-page class="" >
    <div class="full-width header-top-bg bg-primary shadow-up-21"></div>
    <div class="row doc-header full-width" >
      <div class="col-xs-12 col-md-10 offset-md-1 col-lg-8 offset-lg-2 " :class="$q.screen.lt.sm ? '' : 'q-pa-lg'" >
        <q-card class="bg-white " flat bordered  :class="$q.platform.is.mobile ? 'q-ma-sm' : ''">
          <q-card-section horizontal>
            <q-card-section class="col-10">
              <div class="text-h6">{{dataset.nome_old}}</div>
            </q-card-section>
            <q-card-section class="col-2 text-right" v-if="dataset.fotoiconurl">
              <q-avatar size="80px" >
                <q-img :src="dataset.fotoiconurl" contain />
              </q-avatar>
            </q-card-section>
          </q-card-section>
          <q-separator spaced  />
          <q-card-section>
            <div>
              <div class="fit row wrap q-col-gutter-x-md q-col-gutter-y-md q-pb-md">
                <div class="col-md-6 col-xs-12">
                  <q-field outlined label="E-mail" :dense="!$q.platform.is.mobile" stack-label>
                    <template v-slot:prepend>
                      <q-icon name="email" />
                    </template>
                    <template v-slot:control>
                      <div class="self-center full-width no-outline" tabindex="0">{{dataset.email}}</div>
                    </template>
                  </q-field>
                </div>
                <div class="col-md-6 col-xs-12">
                  <q-input outlined v-model="dataset.celular" label="Celular" maxlength="20" type="phone" aria-placeholder="Somente número"
                    :dense="!$q.platform.is.mobile">
                    <template v-slot:prepend>
                      <q-icon name="phone" />
                    </template>
                    <q-tooltip :delay="500">Informe somente número com o DDD</q-tooltip>
                  </q-input>
                </div>
                <div class="col-12">
                  <q-input outlined v-model="dataset.nome" label="Nome" stack-label maxlength="60" type="text" :dense="!$q.platform.is.mobile"  />
                </div>
              </div>
            </div>
          </q-card-section>
          <!-- <q-card-section class="q-ma-none q-pa-none"  >
            <q-table :data="dataset.clientes" :columns="columns" dense :loading="loading" id="tableempresas" flat hide-bottom
              row-key="id" :rows-per-page-options="[0]" title="Empresas associadas a mim" >
              <template v-slot:body-cell-razaosocial="props">
                <q-td :props="props" >
                  <div>{{ props.row.razaosocial}}</div>
                  <div class="text-caption" v-if="props.row.fantasia !== '' && (props.row.fantasia !== props.row.razaosocial)">{{ props.row.fantasia}}</div>
                  <div class="text-caption" v-if="props.row.cidade ? props.row.cidade.cidade : false">{{ props.row.cidade.cidade + ' / ' + props.row.cidade.uf}}</div>
                </q-td>
              </template>
              <template v-slot:body-cell-cnpj="props">
                <q-td :props="props" >
                  <div>
                    {{ props.row.cnpj ? $helpers.mascaraDocCPFCNPJ(props.row.cnpj) : '-'}}
                  </div>
                </q-td>
              </template>
              <template v-slot:body-cell-fantasiafollowup="props">
                <q-td :props="props" >
                  <div v-if="(props.row.fantasia_followup ? props.row.fantasia_followup !== '' : false) && (props.row.followupid ? props.row.followupid !== '' : false)">
                    <q-avatar icon="insights" color="indigo" font-size="15px" size="22px" text-color="white"  />
                    <q-tooltip :delay="500">
                      Follow Up ativado
                    </q-tooltip>
                  </div>
                </q-td>
              </template>
            </q-table>
          </q-card-section> -->
          <q-card-section class="text-right text-caption text-accent" v-if="dataset.usernametype === 'celular'">
            <div>Se o número de celular for alterado, será necessário refazer seu acesso. <q-icon name="info" class="q-mr-md" size="20px" /> </div>
          </q-card-section>
          <q-separator  />
          <q-card-section>
              <q-btn label="Salvar" unelevated color="primary" @click="actSave"/>
              <q-btn label="Voltar" color="primary" flat class="q-ml-sm" @click="$router.go(-1)" />
          </q-card-section>
        </q-card>
      </div>
      <q-page-sticky position="top" expand>
        <q-toolbar class="bg-primary text-white">
          <q-btn flat round icon="arrow_back" @click="$router.go(-1)"/>
          <q-toolbar-title>Meu perfil (*Dev)</q-toolbar-title>
          <q-btn  icon="sync" :round="$q.platform.is.mobile" flat @click="refreshData(null)" :loading="loading" />
        </q-toolbar>
      </q-page-sticky>
    </div>
  </q-page>
</div>
</template>

<script>
import Contato from 'src/mvc/models/contato.js'
export default {
  name: 'usuario.meuperfil',
  components: {
  },
  data () {
    var dataset = new Contato()
    return {
      dataset,
      rows: [],
      filter: '',
      breadcrumbslist: [
      ],
      columns: [
        { name: 'razaosocial', field: 'razaosocial', required: true, label: 'Razão Social', align: 'left', sortable: false },
        { name: 'cnpj', style: 'width: 120px', field: 'cnpj', label: 'CNPJ', align: 'center', sortable: false },
        { name: 'fantasiafollowup', style: 'width: 80px', align: 'center', label: 'Follow Up', field: 'fantasia_followup', sortable: false }
      ],
      showpedido: false,
      tab: 'dados',
      slide: 0,
      loading: false,
      posting: false,
      msgError: '',
      showfavoritos: true
    }
  },
  async mounted () {
    await this.init()
  },
  computed: {
    usuariologado: function () {
      return (this.$store.state.usuario.user ? this.$store.state.usuario.user.id > 0 : false) ? this.$store.state.usuario.user : null
    }
  },
  methods: {
    async init () {
      var app = this
      app.loading = true
      app.dataset.limpardados()
      await app.refreshData()
      app.loading = false
    },
    async refreshData () {
      var app = this
      app.loading = true
      var ret = await app.dataset.meuUsuario()
      if (!ret.ok) {
        app.loading = false
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {
        })
      }
      app.loading = false
    },
    async actSave () {
      var app = this
      app.loading = true
      var celularalterado = ((app.dataset.usernametype === 'celular') && (app.dataset.celular_old !== app.dataset.celular))
      var ret = await app.dataset.saveMeuUsuario()
      if (!ret.ok) {
        app.loading = false
        var a = app.$helpers.showDialog(ret)
        await a.then(function () {
        })
      } else {
        app.$q.notify({
          color: 'positive',
          icon: 'check_circle',
          timeout: 2500,
          message: 'Informações do usuário foram salvas!'
        })
        if (celularalterado) {
          app.$store.dispatch('usuario/logout')
          app.$q.notify({
            color: 'orange',
            icon: 'person',
            timeout: 2500,
            message: 'Celular alterado, necessário fazer novo login!'
          })
        } else {
          await app.$store.dispatch('usuario/getlocalstorage')
        }
      }
      app.loading = false
    }
  }
}
</script>

<style lang="stylus">
</style>
