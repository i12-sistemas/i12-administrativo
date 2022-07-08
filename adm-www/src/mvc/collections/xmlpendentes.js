import Vue from 'vue'
import axios from 'axios'
import XMLPendente from 'src/mvc/models/xmlpendente.js'

class XMLPendentes {
  constructor () {
    this.limpardados()
  }

  async limpardados () {
    this.consultado = false
    this.cliente = null
    this.notas = null
    this.accesscode = null
  }

  async fetch (token, recaptchatoken) {
    var self = this
    self.limpardados()
    let params = {
      token: token,
      'g-recaptcha-response': recaptchatoken
    }
    var req = axios.create({
      baseURL: Vue.prototype.$configini.API_URL,
      withCredentials: false,
      headers: {
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': 'Authorization',
        'Access-Control-Allow-Methods': 'GET, POST, OPTIONS, PUT, PATCH, DELETE',
        'Content-Type': 'application/json;charset=UTF-8'
      }
    })

    let ret = await req.get('/v1/publico/notas/xmlpendente', { params: params }).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          data = data.data
          self.cliente = (data.cliente ? data.cliente : null)
          self.accesscode = (data.accesscode ? data.accesscode : null)
          self.notas = []
          if (data.notas) {
            for (let index = 0; index < data.notas.length; index++) {
              const element = data.notas[index]
              let p = new XMLPendente(element)
              if (p.notanumero > 0) self.notas.push(p)
            }
          }
        }
      }
      self.consultado = true
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }
}
export default XMLPendentes
