import Cliente from 'src/mvc/models/cliente.js'
// import Motorista from 'src/mvc/models/motorista.js'
import Usuario from 'src/mvc/models/usuario.js'
// import NFe from 'src/mvc/models/nfe.js'
// import ColetasEventos from 'src/mvc/collections/coletaseventos.js'
// import moment from 'moment'
// import constantes from './constantes'
import { FollowUpStatusErrosColeta, FollowUpStatusErrosAgenda, FollowUpStatusErrosDtPromessa, FollowUpStatusInicioFup, FollowUpStatusConfirmacaoColeta } from './enums/followuptypes'

class FollowUp {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.cliente = new Cliente()
    this.fornecedorid = null
    // this.fornecedor = new Cliente()
    this.updated_usuario = new Usuario()
    this.erroagendastatus = new FollowUpStatusErrosAgenda('0')
    this.erroagenda = null
    this.errocoletastatus = new FollowUpStatusErrosColeta('0')
    this.errocoleta = null
    this.errodtpromessastatus = new FollowUpStatusErrosDtPromessa('0')
    this.errodtpromessa = null
    this.coletaid = null
    this.statusconfirmacaocoleta = new FollowUpStatusConfirmacaoColeta('0')
    this.iniciofollowup = new FollowUpStatusInicioFup('0')
    this.requisicao = null
    this.ordemcompra = null
    this.ordemcompradig = null
    this._datasolicitacao = null
    this.datapromessa = null
    this.dataconfirmacao = null
    this.comprador = null
    this.fornecrazao = null
    this.forneccnpj = null
    this.forneccidade = null
    this.fornecuf = null
    this.fornectelefone = null
    this.contato = null
    this.email = null
    this.itemid = null
    this.itemdescricao = null
    this.itemnumerolinhapedido = null
    this.qtdesolicitada = null
    this.qtderecebida = null
    this.qtdedevida = null
    this.dhimportacao = null
    this._observacao = null
    this.aprovacaorc = null
    this.normalurgente = null
    this.compradelegada = null
    this.tipooc = null
    this.statusoc = null
    this.statusliberacao = null
    this.situacaolinha = null
    this.compradoracordo = null
    this.datanecessidaderc = null
    this.criacaooc = null
    this.aprovacaooc = null
    this.dataliberacao = null
    this.diaatraso = null
    this.condpagto = null
    this.grupo = null
    this.familia = null
    this.subfamilia = null
    this.udm = null
    this.vlrunitario = null
    this.vlrultcompra = null
    this.moeda = null
    this.totallinhaoc = null
    this.dataultimaentrada = null
    this.tipofrete = null
    this.notafiscal = null
    this.dataagendamentocoleta = null
    this.datacoleta = null
    this.datahora_followup = null
    this.datahoralancamento = null
    this.tipoorigem = null
    this.log = null
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    if (item.id) this.id = item.id
    if (item.fornecedorid) this.fornecedorid = item.fornecedorid
    if (item.updated_usuario) await self.updated_usuario.cloneFrom(item.updated_usuario)
    if (item.erroagendastatus) this.erroagendastatus.value = item.erroagendastatus
    if (item.erroagenda) this.erroagenda = item.erroagenda
    if (item.errocoletastatus) this.errocoletastatus.value = item.errocoletastatus
    if (item.errocoleta) this.errocoleta = item.errocoleta
    if (item.errodtpromessa) this.errodtpromessa = item.errodtpromessa
    if (item.errodtpromessastatus) this.errodtpromessastatus.value = item.errodtpromessastatus
    if (item.coletaid) this.coletaid = item.coletaid
    if (item.statusconfirmacaocoleta) this.statusconfirmacaocoleta.value = item.statusconfirmacaocoleta
    if (item.iniciofollowup) this.iniciofollowup.value = item.iniciofollowup
    if (item.requisicao) this.requisicao = item.requisicao
    if (item.ordemcompra) this.ordemcompra = item.ordemcompra
    if (item.ordemcompradig !== 'undefined') this.ordemcompradig = item.ordemcompradig
    if (item.datasolicitacao) this._datasolicitacao = item.datasolicitacao
    if (item.datapromessa) this.datapromessa = item.datapromessa
    if (item.dataconfirmacao) this.dataconfirmacao = item.dataconfirmacao
    if (item.comprador) this.comprador = item.comprador
    if (item.fornecrazao) this.fornecrazao = item.fornecrazao
    if (item.forneccnpj) this.forneccnpj = item.forneccnpj
    if (item.forneccidade) this.forneccidade = item.forneccidade
    if (item.fornecuf) this.fornecuf = item.fornecuf
    if (item.fornectelefone) this.fornectelefone = item.fornectelefone
    if (item.contato) this.contato = item.contato
    if (item.email) this.email = item.email
    if (item.itemid) this.itemid = item.itemid
    if (item.itemdescricao) this.itemdescricao = item.itemdescricao
    if (item.itemnumerolinhapedido) this.itemnumerolinhapedido = item.itemnumerolinhapedido
    if (item.qtdesolicitada !== 'undefined') this.qtdesolicitada = item.qtdesolicitada
    if (item.qtderecebida !== 'undefined') this.qtderecebida = item.qtderecebida
    if (item.qtdedevida !== 'undefined') this.qtdedevida = item.qtdedevida
    if (item.dhimportacao) this.dhimportacao = item.dhimportacao
    if (item.observacao) this._observacao = item.observacao
    if (item.aprovacaorc) this.aprovacaorc = item.aprovacaorc
    if (item.normalurgente) this.normalurgente = item.normalurgente
    if (item.compradelegada) this.compradelegada = item.compradelegada
    if (item.tipooc) this.tipooc = item.tipooc
    if (item.statusoc) this.statusoc = item.statusoc
    if (item.statusliberacao) this.statusliberacao = item.statusliberacao
    if (item.situacaolinha) this.situacaolinha = item.situacaolinha
    if (item.compradoracordo) this.compradoracordo = item.compradoracordo
    if (item.datanecessidaderc) this.datanecessidaderc = item.datanecessidaderc
    if (item.criacaooc) this.criacaooc = item.criacaooc
    if (item.aprovacaooc) this.aprovacaooc = item.aprovacaooc
    if (item.dataliberacao) this.dataliberacao = item.dataliberacao
    if (item.diaatraso) this.diaatraso = item.diaatraso
    if (item.condpagto) this.condpagto = item.condpagto
    if (item.grupo) this.grupo = item.grupo
    if (item.familia) this.familia = item.familia
    if (item.subfamilia) this.subfamilia = item.subfamilia
    if (item.udm) this.udm = item.udm
    if (typeof item.cliente !== 'undefined') this.cliente = new Cliente(item.cliente)
    // if (typeof item.fornecedor !== 'undefined') this.fornecedor = new Cliente(item.fornecedor)

    if (item.vlrunitario !== 'undefined') this.vlrunitario = item.vlrunitario
    if (item.vlrultcompra !== 'undefined') this.vlrultcompra = item.vlrultcompra
    if (item.moeda) this.moeda = item.moeda
    if (item.totallinhaoc) this.totallinhaoc = item.totallinhaoc
    if (item.dataultimaentrada) this.dataultimaentrada = item.dataultimaentrada
    if (item.tipofrete) this.tipofrete = item.tipofrete
    if (item.notafiscal) this.notafiscal = item.notafiscal
    if (item.dataagendamentocoleta) this.dataagendamentocoleta = item.dataagendamentocoleta
    if (item.datacoleta) this.datacoleta = item.datacoleta
    if (item.datahora_followup) this.datahora_followup = item.datahora_followup
    if (item.datahoralancamento) this.datahoralancamento = item.datahoralancamento
    if (item.tipoorigem) this.tipoorigem = item.tipoorigem
  }

  get observacao () { return this._observacao ? this._observacao.toString().toUpperCase() : '' }
  set observacao (val) {
    this._observacao = val ? val.toString().toUpperCase() : null
  }

  get datasolicitacao () { return this._datasolicitacao }
  set datasolicitacao (val) {
    this._datasolicitacao = val
    // if (typeof val === 'undefined') {
    //   this._datasolicitacao = null
    // } else {
    //   this._datasolicitacao = Vue.prototype.$helpers.DateSlashToData(val)
    // }
  }
}

export default FollowUp
