<template>
<div>
  <q-page class="" >
    <div class="full-width header-top-bg bg-primary shadow-up-21"></div>
    <div class="row doc-header full-width" >
      <div class="col-xs-12 col-md-10 offset-md-1 col-lg-8 offset-lg-2 " :class="$q.screen.lt.sm ? '' : 'q-pa-lg'" >
        <q-card class="bg-white " flat bordered  :class="$q.platform.is.mobile ? 'q-ma-sm' : ''">
          <q-card-section>
            <div class="row">
              <div class="col-12 full-width text-h6">
                Bem-vindo <b>{{usuariologado.nome ? usuariologado.nome : ''}}</b>
              </div>
              <div class="col-12 full-width text-caption">
                Empresa: <b>{{clientelogado.nome ? clientelogado.nome : ''}}</b>
              </div>
            </div>
          </q-card-section>
          <q-separator spaced  v-if="usuariologado.ehoperador" />
          <!-- meus atendimento -->
          <q-card-section v-if="usuariologado.ehoperador" >
            <div class="row q-col-gutter-sm">
              <div class="col-6 text-body">
                Meus atendimentos
              </div>
              <div class="col-6 text-right">
                <q-btn flat icon="add" :label="$q.platform.is.mobile ?  'Novo' : 'Novo atendimento'" :to="{ name: 'atendimentos.novo' }"/>
                <q-btn flat color="grey" icon="sync"  @click="actRefreshDashboarAtendimento" round dense :loading="loadingdashboard" />
              </div>
              <div class="col-xs-12 col-md-4 text-h6" >
                <q-card class="bg-light-blue-3 cursor-pointer  full-height" flat bordered clickable v-ripple @click="actDashboarAtendimento('emabertos')">
                  <q-card-section>
                    <div class="row">
                      <div class="col-8 text-h6">
                        Em abertos
                        <div class=" row text-caption">Atendimentos em processo de atendimento</div>
                      </div>
                      <div class="col-4 text-right text-h3 text-weight-bold">
                        <span v-if="usuariologado.dashboard_atendimento ? typeof usuariologado.dashboard_atendimento.emabertos !== 'undefined' : false">
                          {{usuariologado.dashboard_atendimento.emabertos}}
                        </span>
                        <span v-else>?</span>
                      </div>
                    </div>
                  </q-card-section>
                </q-card>
              </div>
              <div class="col-xs-12 col-md-4 text-h6">
                <q-card class="bg-deep-orange-4 cursor-pointer full-height" flat bordered clickable v-ripple @click="actDashboarAtendimento('naolidos')">
                  <q-card-section>
                    <div class="row">
                      <div class="col-8 text-h6">
                        Não lidos
                        <div class=" row text-caption">Em abertos e encerrados nos últimos 15 dias</div>
                      </div>
                      <div class="col-4 text-right text-h3 text-weight-bold">
                        <span v-if="usuariologado.dashboard_atendimento ? typeof usuariologado.dashboard_atendimento.naolidos !== 'undefined' : false">
                          {{usuariologado.dashboard_atendimento.naolidos}}
                        </span>
                        <span v-else>?</span>
                      </div>
                    </div>
                  </q-card-section>
                </q-card>
              </div>
              <div class="col-xs-12 col-md-4 text-h6">
                <q-card class="bg-purple-3 cursor-pointer  full-height" flat bordered clickable v-ripple @click="actDashboarAtendimento('pendenteresposta')">
                  <q-card-section>
                    <div class="row">
                      <div class="col-8 text-h6">
                        Aguardando
                        <div class=" row text-caption">Atendimentos aguardando minha resposta</div>
                      </div>
                      <div class="col-4 text-right text-h3 text-weight-bold">
                        <span v-if="usuariologado.dashboard_atendimento ? typeof usuariologado.dashboard_atendimento.pendenteresposta !== 'undefined' : false">
                          {{usuariologado.dashboard_atendimento.pendenteresposta}}
                        </span>
                        <span v-else>?</span>
                      </div>
                    </div>
                  </q-card-section>
                </q-card>
              </div>
              <div class="col-xs-12 col-md-4 text-h6" v-if="$q.screen.lt.sm">
                <q-card class="bg-grey-2 cursor-pointer " flat bordered clickable v-ripple @click="$router.push({ name: 'atendimentos.novo' })">
                  <q-card-section>
                    <div class="row">
                      <div class="col-8 text-h6">
                        Novo Atendimento
                        <div class=" row text-caption">Clique aqui pra iniciar um novo atendimento</div>
                      </div>
                      <div class="col-4 text-right text-h3 text-weight-bold">
                        <q-icon name="add" />
                      </div>
                    </div>
                  </q-card-section>
                </q-card>
              </div>
            </div>
          </q-card-section>
          <!-- meus atendimento -->
        </q-card>
          <q-page-sticky position="top" expand>
            <q-toolbar class="bg-primary text-white">
              <q-btn flat dense round @click="$store.dispatch('homedashboard/togglemenu')" aria-label="Menu" icon="menu" />
              <q-toolbar-title>i12 Sistemas</q-toolbar-title>
              <q-space />
              <q-btn flat dense round @click="$store.dispatch('homedashboard/togglemenu')" icon="perm_identity" />
            </q-toolbar>
          </q-page-sticky>
        </div>
    </div>
  </q-page>
</div>
</template>

<script>
export default {
  name: 'home',
  components: {
  },
  data () {
    return {
      loadingdashboard: false
    }
  },
  computed: {
    usuariologado: function () {
      var app = this
      let u = null
      if (app.$store.state.usuario.logado) {
        if (app.$store.state.usuario.usuario) {
          u = app.$store.state.usuario.usuario
        }
      }
      return u
    },
    clientelogado: function () {
      var app = this
      let u = null
      if (app.$store.state.usuario.logado) {
        if (app.$store.state.usuario.cliente) {
          u = app.$store.state.usuario.cliente
        }
      }
      return u
    }
  },
  created: function () {
    this.$eventbus.$on('usuariodash_loading', this.onusuariodash_loading)
    this.$eventbus.$on('usuariodash_updated', this.onusuariodash_updated)
  },
  beforeDestroy: function () {
    this.$eventbus.$off('usuariodash_loading', this.onusuariodash_loading)
    this.$eventbus.$off('usuariodash_updated', this.onusuariodash_updated)
  },
  async mounted () {
  },
  methods: {
    async actDashboarAtendimento (item) {
      var app = this
      app.$nextTick(() => {
        app.$router.push({ name: 'atendimentos', query: { situacao: item } })
      })
    },
    onusuariodash_loading () { this.loadingdashboard = true },
    onusuariodash_updated () {
      var app = this
      app.$nextTick(() => {
        app.loadingdashboard = false
      })
    },
    async actRefreshDashboarAtendimento () {
      var app = this
      app.loadingdashboard = true
      this.$eventbus.$emit('usuariodash', true)
      app.$nextTick(() => {
        app.loadingdashboard = false
      })
    }
  }
}
</script>
