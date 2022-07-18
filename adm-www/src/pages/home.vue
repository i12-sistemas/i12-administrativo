<template>
<div>
  <q-page class="">
    <div class="full-width header-top-bg bg-primary shadow-up-21"></div>
    <div class="row doc-header" >
      <div class="col-sm-12" :class="$q.platform.is.mobile ? '' : 'q-pa-lg'" >
        <q-card class="bg-white " flat bordered  :class="$q.platform.is.mobile ? 'q-ma-sm' : ''">
            <q-card-section>
              <div class="row">
                <div class="col-12 full-width row justify-center">
                  <lottie-animation  :animationData="lottieindex" :speed="1" :loop="true" autoPlay style="max-width: 400px" />
                </div>
              </div>
            </q-card-section>
          </q-card>
          <q-page-sticky position="top" expand>
            <q-toolbar class="bg-primary text-white">
              <q-btn flat dense round @click="$store.dispatch('homedashboard/togglemenu')" aria-label="Menu" icon="menu" />
              <q-separator vertical inset v-if="!$q.platform.is.mobile" spaced/>
              <q-toolbar-title>Portal Administrativo</q-toolbar-title>
            </q-toolbar>
          </q-page-sticky>
        </div>
    </div>
  </q-page>
</div>
</template>

<script>
var lottieindex = require('src/assets/lotties/remote-work-management.json')
export default {
  name: 'home',
  components: {
  },
  data () {
    return {
      lottieindex
    }
  },
  async mounted () {
  },
  methods: {
  }
}
</script>
