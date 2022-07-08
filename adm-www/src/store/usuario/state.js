export default function () {
  return {
    logado: false,
    token: null,
    expireat: null,
    login: null,

    // dados locais após checklogin
    usuario: null,
    ret: null,
    axioserror401count: 0
  }
}
