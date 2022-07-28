<template>
  <q-layout view="lHh lpR fff">
    <q-page-container class="bg-lyt">
      <q-page class="row justify-center full-width  " :class="$q.platform.is.mobile ? '' : 'full-height items-center'">
        <div class="column col-xs-12 col-sm-10 col-lg-8">
          <transition name="fade">
            <router-view :key="$route.fullPath"  />
          </transition>
        </div>
      </q-page>
    </q-page-container>
    <q-footer  >
      <div class="bg-lyt text-grey-7 text-caption q-pa-xs text-center">
        <div><span class="text-weight-bold">{{appPackage.productName}} :: {{appPackage.description}}</span> | Versão {{appPackage.version}} release {{$helpers.datetimeToBR(appPackage.releasedatetime)}}</div>
        <div>© Copyright {{year ? year : '-'}} :: <a href="https://www.i12.com.br" target="_blank">i12.com.br</a></div>
      </div>
    </q-footer>
  </q-layout>
</template>

<script>
import datapackage from '../../package.json'
import { openURL } from 'quasar'
import moment from 'moment'
export default {
  name: 'lytLoginDispositivo',
  data () {
    return {
      appPackage: datapackage,
      year: 2021,
      tab: 'mails',
      slide: 1,
      autoplay: true
    }
  },
  async mounted () {
    var app = this
    app.year = moment().year()
  },
  meta () {
    return {
      title: datapackage.productName + ' :: ' + moment().year(),
      description: { name: datapackage.description, content: 'Page 1' }
    }
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
    background: radial-gradient(circle, rgb(230, 230, 230) 0%, rgb(255, 255, 255) 100%);
}
.background-image {
  position: fixed;
  left: 0;
  right: 0;
  z-index: 0;
  display: block;
  width: 100%;
  height: 100%;
  -webkit-filter: blur(5px);
  -moz-filter: blur(5px);
  -o-filter: blur(5px);
  -ms-filter: blur(5px);
  filter: blur(5px);
  opacity: 0.1;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
}
.bg-card {
    width: 100%;
    height: 100%;
    opacity: 1;
    visibility: inherit;
    z-index: 20;
    border: 6px solid rgba(255, 255, 255, 0.541);
}
.bg-gradiente {
    /* Center and scale the image nicely */
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    background: rgb(253 253 253);
    /* background: linear-gradient(0deg, rgb(0 0 0 / 45%) 0%, rgb(245 245 245 / 0%) 100%); */
    /* background-repeat: no-repeat; */
    /* background-image: url(http://162.214.100.18/~conecta/admin/public/img/thumb-slider-2-1084831205.jpg); */
    /* background-size: cover; */
}
.textconecta {
    text-shadow: rgb(253 253 253) 1px 1px;
    font-weight: bold;
    width: 50%;
    font-size: x-large;
    background-color: rgb(253 253 253);
    padding: 10px;
    border-radius: 16px;
}
.custom-caption {
  text-align: center;
  padding: 12px;
  color: white;
  background-color: rgba(0, 0, 0, .3);
}
.myshadow {
  -webkit-box-shadow: -2px 4px 41px -14px rgba(217,217,217,1);
  -moz-box-shadow: -2px 4px 41px -14px rgba(217,217,217,1);
  box-shadow: -2px 4px 41px -14px rgba(217,217,217,1) !important;
}
</style>
