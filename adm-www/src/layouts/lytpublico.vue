<template>
  <q-layout view="hHh lpR lff" class="bg-grey-1" >
    <q-header bordered class="bg-white  text-primary print-hide">
      <div class="full-width row justify-center " :class="$q.platform.is.mobile ? '' : 'full-height items-center'">
        <div class="column col-xs-12 col-sm-11 col-lg-8 col-xl-8 bg-white">
          <div class="row full-width">
            <q-toolbar>
              <q-space />
              <q-toolbar-title shrink class="row items-center no-wrap cursor-pointer" >
                <img src="~assets/Logo-i12-horizontal150x50.png" style="max-height: 50px;" @click="$router.push({ name: 'home' })" >
              </q-toolbar-title>
              <q-space />
            </q-toolbar>
          </div>
        </div>
      </div>
    </q-header>
    <q-page-container>
      <transition name="fade">
        <router-view :key="$route.fullPath"  />
      </transition>
    </q-page-container>
    <q-footer :class="$q.platform.is.mobile ? 'bg-primary text-grey-1' : 'bg-grey-2 text-grey-8'"  bordered v-if="$route.name === 'home'">
      <div class="q-mt-md  text-caption q-pa-xs text-center">
        <div><span class="text-weight-bold">{{appPackage.productName}} :: {{appPackage.description}}</span> | Versão {{appPackage.version}} release {{$helpers.datetimeToBR(appPackage.releasedatetime)}}</div>
        <div>© Copyright {{year ? year : ''}} :: <a href="https://www.i12.com.br" target="_blank">i12.com.br</a></div>
      </div>
    </q-footer>
  </q-layout>
</template>

<script>
import moment from 'moment'
import datapackage from '../../package.json'
import { openURL } from 'quasar'
export default {
  name: 'lytLoginDispositivo',
  data () {
    return {
      appPackage: datapackage,
      year: 2021
    }
  },
  mounted () {
    var app = this
    app.year = moment().year()
  },
  computed: {
  },
  methods: {
    openURL,
    async closeApp () {
      // var app = this
      navigator.app.exitApp()
    }
  }
}
</script>

<style scoped>
.bg-lyt {
    background: rgb(255, 255, 255);
    background: radial-gradient(circle, rgb(255, 255, 255) 0%, rgb(204 221 237) 100%);
}

</style>
