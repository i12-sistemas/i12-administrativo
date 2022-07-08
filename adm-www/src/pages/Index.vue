<template>
<div>
  <q-page class="">
    <div class="row doc-page" :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-pa-md'" >
      <div class="col-12 full-width" :class="$q.platform.is.mobile ? 'q-pa-0' : 'q-pt-md'">
        <div class="q-mt-xl full-width text-center">
          <!-- <q-btn unelevated  size="lg" color="green" :to="{ name: 'followup.dashboard1' }" icon="follow_the_signs" label="Relatório FollowUp"
            v-if="usuariologado.cliente.followupid ? usuariologado.cliente.followupid !== '' : false">
            <q-tooltip :delay="700">Follow Up</q-tooltip>
          </q-btn> -->
        </div>
        <div class="q-mt-xl full-width text-center">
          <img alt="Conecta Transportes" src="~assets/Logo-i12-horizontal150x50.png">
        </div>
      </div>
    </div>
  </q-page>
</div>
</template>

<script>
export default {
  name: 'PageIndex',
  async mounted () {
    // var app = this
    // this.$store.dispatch('usuario/doLogout')
  },
  computed: {
    usuariologado: function () {
      var app = this
      let u = null
      if (app.$store.state.usuario.logado) {
        if (app.$store.state.usuario.user) {
          u = app.$store.state.usuario.user
        }
      }
      return u
    }
  }
}
</script>
