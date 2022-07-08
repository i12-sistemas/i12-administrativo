import Vue from 'vue'
import Usuario from 'src/mvc/models/usuario.js'
import Cliente from 'src/mvc/models/cliente.js'
import DialogClienteAdd from 'src/pages/cadastro/clienteusuarios/clienteadddialog.vue'
import DialogUsuarioAdd from 'src/pages/cadastro/clienteusuarios/clienteusuarioadddialog.vue'
import DialogUsuarioEdit from 'src/pages/cadastro/clienteusuarios/clienteusuarioeditdialog.vue'

class ClienteUsuario {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.clientecount = 0
    this.clientes = null
    this.nome = ''
    this.email = ''
    this.celular = ''
    this.ativo = true
    this.pemitefup = false
    this.permitecoletasver = false
    this.permitestatusgeral = false
    this.clienteusuariodadmin = 0
    this.fotourl = null
    this.ultimoacesso = null
    this.created_at = null
    this.updated_at = null
    this.created_usuario = null
    this.updated_usuario = null
  }

  async find (pIDorEmail) {
    var self = this
    self.limpardados()
    let ret = await Vue.prototype.$axios.get('v1/painelcliente/usuarios/' + pIDorEmail).then(async response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          await self.cloneFrom(data.data)
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async save () {
    var self = this
    try {
      var permite = Vue.prototype.$helpers.permite('cadastros.clientes.usuarios.gerenciar')
      if (!permite.ok) throw new Error(permite.msg)

      var novousuario = self.id ? !(self.id > 0) : true

      if (novousuario) {
        if (!Vue.prototype.$helpers.validaEmail(self.email)) throw new Error('E-mail inválido')
        if (self.nome ? self.nome === '' : true) throw new Error('Nome inválido')
        if (typeof self.clienteid === 'undefined') throw new Error('Necessário informar um cliente para criar um usuário')
        if (!(self.clienteid > 0)) throw new Error('Necessário informar um cliente para criar um usuário')
      }
    } catch (error) {
      return { ok: false, msg: error.message, warning: true }
    }
    var params = null
    var req = null
    if (novousuario) {
      params = {
        ativo: 1,
        nome: self.nome,
        email: self.email,
        clienteid: self.clienteid
      }
      if (typeof self.celular !== 'undefined') params['celular'] = self.celular

      req = Vue.prototype.$axios.post('v1/painelcliente/usuarios', params)
    } else {
      params = {
        ativo: self.ativo ? 1 : 0
      }
      req = Vue.prototype.$axios.put('v1/painelcliente/usuarios', params)
    }

    let ret = await req.then(async response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          if (data.data) await self.cloneFrom(data.data)
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async savePermissao () {
    var self = this
    try {
      // var permite = Vue.prototype.$helpers.permite('cadastros.clientes.usuarios.gerenciar')
      // if (!permite.ok) throw new Error(permite.msg)
      //  if (self.id ? !(self.id > 0) : true) throw new Error('Somente para usuários cadastrados')
    } catch (error) {
      return { ok: false, msg: error.message, warning: true }
    }
    var params = {
      pemitefup: self.pemitefup ? 1 : 0,
      permitecoletasver: self.permitecoletasver ? 1 : 0,
      permitestatusgeral: self.permitestatusgeral ? 1 : 0
    }
    var req = Vue.prototype.$axios.post('v1/painelcliente/usuarios/' + self.id + '/savepermissao', params)
    let ret = await req.then(async response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async clienteAdd (pClienteId) {
    var self = this
    try {
      // var permite = Vue.prototype.$helpers.permite('cadastros.clientes.usuarios.gerenciar')
      // if (!permite.ok) throw new Error(permite.msg)

      if (self.id ? !(self.id > 0) : false) throw new Error('Nenhum usuário selecionado')
      if (pClienteId ? !(pClienteId > 0) : false) throw new Error('Nenhum cliente informado')
    } catch (error) {
      return { ok: false, msg: error.message, warning: true }
    }
    var params = {
      clienteid: pClienteId
    }
    var req = Vue.prototype.$axios.post('v1/painelcliente/usuarios/' + self.id + '/cliente/add', params)

    let ret = await req.then(async response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          if (data.data) await self.cloneFrom(data.data)
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async clienteRemove (pClienteId) {
    var self = this
    try {
      // var permite = Vue.prototype.$helpers.permite('cadastros.clientes.usuarios.gerenciar')
      // if (!permite.ok) throw new Error(permite.msg)

      // if (self.id ? !(self.id > 0) : false) throw new Error('Nenhum usuário selecionado')
      // if (pClienteId ? !(pClienteId > 0) : false) throw new Error('Nenhum cliente informado')
    } catch (error) {
      return { ok: false, msg: error.message, warning: true }
    }
    var req = Vue.prototype.$axios.delete('v1/painelcliente/usuarios/' + self.id + '/cliente/' + pClienteId)

    let ret = await req.then(async response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
          if (data.data) await self.cloneFrom(data.data)
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (typeof item.id !== 'undefined') self.id = parseInt(item.id)
    if (typeof item.clientecount !== 'undefined') self.clientecount = parseInt(item.clientecount)
    if (typeof item.nome !== 'undefined') self.nome = item.nome.toString().toUpperCase()
    self.nome_old = item.nome.toString().toUpperCase()
    if (typeof item.celular !== 'undefined') self.celular = item.celular
    if (typeof item.email !== 'undefined') self.email = item.email
    if (typeof item.fotourl !== 'undefined') self.fotourl = item.fotourl
    self.ativo = Vue.prototype.$helpers.toBool(item.ativo)
    self.pemitefup = Vue.prototype.$helpers.toBool(item.pemitefup)
    self.permitecoletasver = Vue.prototype.$helpers.toBool(item.permitecoletasver)
    self.permitestatusgeral = Vue.prototype.$helpers.toBool(item.permitestatusgeral)
    if (typeof item.ultimoacesso !== 'undefined') self.ultimoacesso = item.ultimoacesso
    if (typeof item.created_at !== 'undefined') self.created_at = item.created_at
    if (typeof item.updated_at !== 'undefined') self.updated_at = item.updated_at
    if (typeof item.created_usuario !== 'undefined') self.created_usuario = new Usuario(item.created_usuario, true)
    if (typeof item.updated_usuario !== 'undefined') self.updated_usuario = new Usuario(item.updated_usuario, true)

    if (typeof item.clientes !== 'undefined') {
      if (item.clientes ? item.clientes.length > 0 : false) {
        self.clientes = []
        for (let index = 0; index < item.clientes.length; index++) {
          var lItem = new Cliente(item.clientes[index])
          self.clientes.push(lItem)
        }
      }
    }
  }

  async reativarWithQuestion (app) {
    var self = this
    try {
      // var permite = Vue.prototype.$helpers.permite('cadastros.clientes.usuarios.gerenciar')
      // if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    return new Promise((resolve) => {
      app.$q.dialog({
        title: 'Reativar acesso do usuário?',
        message: 'Usuário: <b>' + self.nome + '</b>',
        html: true,
        ok: {
          label: 'Reativar',
          unelevated: true
        },
        cancel: true
      }).onOk(async data => {
        var ret = await self.reativar()
        resolve(ret)
      }).onCancel(() => {
        resolve({ ok: false })
      })
    })
  }

  async reativar () {
    var self = this
    try {
      // var permite = Vue.prototype.$helpers.permite('cadastros.clientes.usuarios.gerenciar')
      // if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }

    let ret = await Vue.prototype.$axios.post('v1/painelcliente/usuarios/' + self.id + '/reativar').then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async deleteWithQuestion (app) {
    var self = this
    try {
      // var permite = Vue.prototype.$helpers.permite('cadastros.clientes.usuarios.gerenciar')
      // if (!permite.ok) throw new Error(permite.msg)
      var password = await Vue.prototype.$helpers.randomIntFromInterval(100, 999)
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    return new Promise((resolve) => {
      app.$q.dialog({
        title: (self.ultimoacesso ? 'Inativar' : 'Excluir') + ' usuário?',
        message: 'Para ' + (self.ultimoacesso ? 'inativar' : 'excluir') + ' o usuário "' + self.nome + '" digite o número <b><i>' + password + '</i></b>?',
        html: true,
        prompt: {
          model: '',
          outlined: true,
          label: 'Número de confirmação',
          type: 'number'
        },
        cancel: true
      }).onOk(async data => {
        if (parseInt(data) === password) {
          var ret = await self.delete()
          resolve(ret)
        } else {
          resolve({ ok: false, msg: 'Informação inválida', warning: true })
        }
      }).onCancel(() => {
        resolve({ ok: false })
      })
    })
  }

  async deleteClienteAssociacaoWithQuestion (app, pCliente) {
    var self = this
    try {
      // var permite = Vue.prototype.$helpers.permite('cadastros.clientes.usuarios.gerenciar')
      // if (!permite.ok) throw new Error(permite.msg)
      var password = await Vue.prototype.$helpers.randomIntFromInterval(100, 999)
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    return new Promise((resolve) => {
      app.$q.dialog({
        title: 'Remover associação cliente X usuário?',
        message: 'Para remover a associação do cliente <b>"' + pCliente.razaosocial + '"</b> com o usuário <b>"' + self.nome + '"</b> digite o número <b><i>' + password + '</i></b>?',
        html: true,
        prompt: {
          model: '',
          outlined: true,
          label: 'Número de confirmação',
          type: 'number'
        },
        cancel: true
      }).onOk(async data => {
        if (parseInt(data) === password) {
          var ret = await self.clienteRemove(pCliente.id)
          resolve(ret)
        } else {
          resolve({ ok: false, msg: 'Informação inválida', warning: true })
        }
      }).onCancel(() => {
        resolve({ ok: false })
      })
    })
  }

  async delete () {
    var self = this
    try {
      // var permite = Vue.prototype.$helpers.permite('cadastros.clientes.usuarios.gerenciar')
      // if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    let ret = await Vue.prototype.$axios.delete('v1/painelcliente/usuarios/' + self.id).then(response => {
      let data = response.data
      var ret = { ok: false, msg: '' }
      if (data) {
        ret.msg = data.msg ? data.msg : ''
        if (data.ok) {
          ret.ok = true
        }
      }
      return ret
    }).catch(error => {
      return Vue.prototype.$helpers.errorReturn(error)
    })
    return ret
  }

  async ShowDialogAdd (app, cliente) {
    var self = this
    try {
      // var permite = Vue.prototype.$helpers.permite('cadastros.clientes.usuarios.gerenciar')
      // if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    return new Promise((resolve) => {
      app.$q.dialog({
        parent: app,
        component: DialogUsuarioAdd,
        cliente: cliente,
        usuario: self,
        cancel: true
      }).onOk(async retDados => {
        var ret = retDados
        resolve(ret)
      }).onCancel(() => {
        resolve({ ok: false })
      })
    })
  }

  async ShowDialogEdit (app) {
    var self = this
    try {
      /* var permite = Vue.prototype.$helpers.permite('cadastros.clientes.usuarios.gerenciar')
      if (!permite.ok) throw new Error(permite.msg) */
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    return new Promise((resolve) => {
      app.$q.dialog({
        parent: app,
        component: DialogUsuarioEdit,
        usuario: self,
        cancel: true
      }).onOk(async retDados => {
        var ret = retDados
        resolve(ret)
      }).onCancel(() => {
        resolve({ ok: false })
      })
    })
  }

  async ShowDialogClienteAdd (app) {
    var self = this
    try {
      // var permite = Vue.prototype.$helpers.permite('cadastros.clientes.usuarios.gerenciar')
      // if (!permite.ok) throw new Error(permite.msg)
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    return new Promise((resolve) => {
      app.$q.dialog({
        parent: app,
        component: DialogClienteAdd,
        usuario: self,
        cancel: true
      }).onOk(async retDados => {
        var ret = retDados
        resolve(ret)
      }).onCancel(() => {
        resolve({ ok: false })
      })
    })
  }
}

export default ClienteUsuario
