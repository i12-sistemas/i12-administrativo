export default async ({ store }) => {
  store.dispatch('usuario/checkip')
  await store.dispatch('usuario/init')
}
