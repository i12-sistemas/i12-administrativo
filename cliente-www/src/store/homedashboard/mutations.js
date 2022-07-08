import moment from 'moment'
export const init = async (state, dados) => {
  state.options = dados
}
export const setoption = async (state, dados) => {
  try {
    state.options = dados
    state.ultimaatualizacao = moment.now()
  } catch (error) {
    console.error(error)
  }
}
export const seterror = async (state, dados) => {
  state.error = dados
}
export const setuser = async (state, dados) => {
  state.user = dados
}
