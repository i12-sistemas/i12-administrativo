export const add = async (state, route) => {
  if (!state.lista) state.lista = []
  state.lista.push(route)
}
