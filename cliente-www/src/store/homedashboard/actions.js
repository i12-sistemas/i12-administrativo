import Vue from 'vue'

export async function togglemenu ({ commit, state, dispatch }) {
  Vue.prototype.$eventbus.$emit('togglemenu')
}
