export async function add ({ commit }, route) {
  await commit('add', route)
}
