export default async ({ Vue }) => {
  Vue.prototype.$eventbus = new Vue()
}
