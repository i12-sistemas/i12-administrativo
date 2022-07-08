import Vue from 'vue'
import Vuex from 'vuex'

import usuario from './usuario'
import homedashboard from './homedashboard'
import historyroute from './historyroute'

Vue.use(Vuex)

/*
 * If not building with SSR mode, you can
 * directly export the Store instantiation
 */

export default function ({ app, router, store, Vue }) {
  const Store = new Vuex.Store({
    modules: {
      usuario,
      homedashboard,
      historyroute
    },
    // enable strict mode (adds overhead!)
    // for dev mode only
    strict: process.env.DEV
  })
  return Store
}
