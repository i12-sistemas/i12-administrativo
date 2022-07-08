export default async ({ store }) => {
  await store.dispatch('usuario/getlocalstorage')
  await store.dispatch('homedashboard/init')
  // await store.dispatch('homedashboard/refresh')
}
