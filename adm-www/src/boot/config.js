import configini from 'src/statics/config/index.js'
import icons from 'src/assets/icons.js'
export default async ({ Vue }) => {
  Vue.prototype.$configini = configini
  Vue.prototype.$icons = icons
  Vue.prototype.$qtable = {
    rowsperpageoptions: [20, 50, 100, 250, 500]
  }
}
