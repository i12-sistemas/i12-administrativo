import ColetaItem from 'src/mvc/models/coletaitem.js'
import Cliente from 'src/mvc/models/cliente.js'
import Motorista from 'src/mvc/models/motorista.js'
import Usuario from 'src/mvc/models/usuario.js'
import Endereco from 'src/mvc/models/endereco.js'
import Regiao from 'src/mvc/models/regiao.js'
import NFe from 'src/mvc/models/nfe.js'
import ColetaNota from 'src/mvc/models/coletanota.js'
// import ColetasEventos from 'src/mvc/collections/coletaseventos.js'
import Vue from 'vue'
import moment from 'moment'
import constantes from './constantes'
import { ColetaEncerramentoTipo, ColetaSituacaoTipo, ColetaOrigemTipo, ColetaSituacaoCliente } from './enums/coletatypes'
import DialogAddOrEdit from 'src/pages/operacional/coletas/coleta/editdialog'
// import BaixaDesfazerDialog from 'src/pages/operacional/coletas/coleta/baixadesfazer-dialog'
// import CancelarDialog from 'src/pages/operacional/coletas/coleta/cancelar-dialog'
// import BaixarDialog from 'src/pages/operacional/coletas/coleta/baixar-dialog'

class Coleta {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.origem = new ColetaOrigemTipo('1')
    this.chavenota = ''
    this.nfe = new NFe()
    this.dhcoleta = moment().format('YYYY-MM-DD')
    this.dhbaixa = null
    this.encerramentotipo = new ColetaEncerramentoTipo(null)
    this.situacao = new ColetaSituacaoTipo('1')
    this.statusvirtual = constantes.ColetaStatusVirtual.NAOLIBERADO

    this.regiaocoleta = new Regiao()
    this.enderecocoleta = new Endereco()
    this.motorista = new Motorista()

    this.clienteorigem = new Cliente()
    this.clientedestino = new Cliente()

    // this.eventos = new ColetasEventos(-1)

    this.itens = null
    this.itenscount = 0

    delete this.orcamentoid

    this.nota = null

    this.ctenumero = null
    this.statuscliente = new ColetaSituacaoCliente()

    this.contatonome = ''
    this.contatoemail = ''
    this.peso = 0
    this.qtde = 0
    this.especie = ''
    this.obs = ''
    this.justsituacao = ''
    this.veiculoexclusico = false
    this.cargaurgente = false
    this.produtosperigosos = false

    this.gestaocliente_comprador = ''
    this.gestaocliente_itenscomprador = ''
    this.gestaocliente_ordemcompra = null

    this.created_at = null
    this.updated_at = null
    this.created_usuario = new Usuario()
    this.updated_usuario = new Usuario()
  }

  temclienteorigem () {
    return (this.clienteorigem ? this.clienteorigem.id > 0 : false)
  }
  temclientedestino () {
    return (this.clientedestino ? this.clientedestino.id > 0 : false)
  }

  get chavenota () { return this._chavenota }

  set chavenota (val) {
    this._chavenota = val
    this.nfe = new NFe(val)
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (item.id) this.id = item.id
    if (item.chavenota) {
      this._chavenota = item.chavenota
      this.nfe = new NFe(item.chavenota)
    }
    if (typeof item.statuscliente !== 'undefined') {
      this.statuscliente = new ColetaSituacaoCliente(item.statuscliente)
    } else {
      this.statuscliente = new ColetaSituacaoCliente(1)
    }

    if (item.origem) this.origem.value = item.origem
    if (item.dhcoleta) this.dhcoleta = item.dhcoleta
    if (item.dhbaixa) this.dhbaixa = item.dhbaixa
    if (item.encerramentotipo) this.encerramentotipo.value = item.encerramentotipo
    if (item.situacao) this.situacao.value = item.situacao
    if (item.contatonome) this.contatonome = item.contatonome
    if (item.contatoemail) this.contatoemail = item.contatoemail
    if (item.peso) this.peso = item.peso
    if (item.qtde) this.qtde = item.qtde
    if (item.especie) this.especie = item.especie
    if (item.obs) this.obs = item.obs
    if (item.justsituacao) this.justsituacao = item.justsituacao

    if (typeof item.ctenumero !== 'undefined') this.ctenumero = item.ctenumero

    if (item.orcamentoid) this.orcamentoid = item.orcamentoid

    self.veiculoexclusico = Vue.prototype.$helpers.toBool(item.veiculoexclusico)
    self.cargaurgente = Vue.prototype.$helpers.toBool(item.cargaurgente)
    self.produtosperigosos = Vue.prototype.$helpers.toBool(item.produtosperigosos)

    if (item.gestaocliente_comprador) self.gestaocliente_comprador = item.gestaocliente_comprador
    if (item.gestaocliente_ordemcompra) self.gestaocliente_ordemcompra = item.gestaocliente_ordemcompra
    if (item.gestaocliente_itenscomprador) self.gestaocliente_itenscomprador = item.gestaocliente_itenscomprador

    if (item.endcoleta_logradouro) self.enderecocoleta.logradouro = item.endcoleta_logradouro
    if (item.endcoleta_endereco) self.enderecocoleta.endereco = item.endcoleta_endereco
    if (item.endcoleta_numero) self.enderecocoleta.numero = item.endcoleta_numero
    if (item.endcoleta_bairro) self.enderecocoleta.bairro = item.endcoleta_bairro
    if (item.endcoleta_cep) self.enderecocoleta.cep = item.endcoleta_cep
    if (item.endcoleta_complemento) self.enderecocoleta.complemento = item.endcoleta_complemento
    if (item.endcoleta_cidade) await self.enderecocoleta.cidade.cloneFrom(item.endcoleta_cidade)
    if (item.endcoleta_regiao) await self.regiaocoleta.cloneFrom(item.endcoleta_regiao)

    if (typeof item.motorista !== 'undefined') self.motorista = new Motorista(item.motorista)
    if (item.clienteorigem) self.clienteorigem = new Cliente(item.clienteorigem)
    if (item.clientedestino) self.clientedestino = new Cliente(item.clientedestino)
    if (typeof item.nota !== 'undefined') self.nota = new ColetaNota(item.nota)

    if (item.itenscount) self.itenscount = item.itenscount
    if (item.itens) {
      if (item.itens.length > 0) {
        self.itens = []
        for (let index = 0; index < item.itens.length; index++) {
          var lItem = new ColetaItem(item.itens[index])
          self.itens.push(lItem)
        }
      }
    }
  }

  async find (pID) {
    var self = this
    self.limpardados()
    let ret = await Vue.prototype.$axios.get('v1/painelcliente/coletas/coleta/' + pID).then(async response => {
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

  async dialogAddOrEdit (app) {
    var self = this
    try {
    } catch (error) {
      return { ok: false, msg: error.message, warning: false }
    }
    return new Promise((resolve) => {
      app.$q.dialog({
        parent: app,
        component: DialogAddOrEdit,
        dataset: self,
        adding: !(self.id > 0),
        cancel: true
      }).onOk(async retOk => {
        var ret = { ok: retOk }
        resolve(ret)
      }).onCancel(() => {
        resolve({ ok: false })
      })
    })
  }
}

export default Coleta
