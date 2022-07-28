<template>
  <q-layout view="lHh lpR fff">
    <q-page-container class="bg-lyt">
      <q-page class="full-width row justify-center " :class="$q.platform.is.mobile ? '' : 'full-height items-center'">
        <div class="column col-xs-12 col-sm-11 col-lg-8 col-xl-8">
          <div class="row full-width">
            <q-card class="my-card full-width" flat :bordered="!$q.platform.is.mobile" :class="($q.platform.is.mobile ? '' : 'bg-gradiente')">
              <q-card-section class="q-pa-none">
                <div class="row">
                  <div class="q-pa-sm rounded-borders" :class="($q.screen.gt.sm ? 'col-md-4' : 'col-12') + ' ' +  ($q.platform.is.mobile ? '' : '')">
                    <div class="row flex-center full-width" >
                      <q-card flat class="full-width"  style="border: 6px solid rgba(255, 255, 255, 0.541);" >
                        <q-card-section class="bg-white justify-center items-center text-center">
                          <img src="~assets/Logo-i12-horizontal150x50.png"  width="100%" style="max-width: 300px" >
                        </q-card-section>
                        <q-separator spaced inset  />
                        <q-card-section class="q-pa-none q-ma-none">
                          <transition name="fade">
                            <router-view :key="$route.fullPath"  />
                          </transition>
                        </q-card-section>
                      </q-card>
                    </div>
                  </div>
                  <div class="col-md-8 q-pa-sm" v-if="$q.screen.gt.sm">
                    <lottieinternal arquivo="secure-login" />
                  </div>
                </div>
              </q-card-section>
            </q-card>
          </div>
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
</style>
