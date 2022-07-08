import Usuario from 'src/mvc/models/usuario.js'
import { FUPLogTipoOrigem, FollowUpStatusErrosColeta, FollowUpStatusErrosAgenda, FollowUpStatusErrosDtPromessa, FollowUpStatusInicioFup, FollowUpStatusConfirmacaoColeta } from './enums/followuptypes'

class FollowUpLog {
  constructor (pItem) {
    this.limpardados()
    if (pItem) this.cloneFrom(pItem)
  }

  async limpardados () {
    this.id = null
    this.followupid = null
    this.created_usuario = new Usuario()

    this.created_at = null
    this.datasolicitacao = null

    this.erroagendastatus = new FollowUpStatusErrosAgenda('0')
    this.erroagenda = null
    this.errocoletastatus = new FollowUpStatusErrosColeta('0')
    this.errocoleta = null
    this.errodtpromessastatus = new FollowUpStatusErrosDtPromessa('0')
    this.errodtpromessa = null

    this.coletaid = null
    this.statusconfirmacaocoleta = new FollowUpStatusConfirmacaoColeta('0')
    this.iniciofollowup = new FollowUpStatusInicioFup('0')

    this.ordemcompra = null
    this.ordemcompradig = null
    this.datapromessa = null
    this.dataconfirmacao = null
    this.itemnumerolinhapedido = null
    this.qtdesolicitada = null
    this.qtderecebida = null
    this.qtdedevida = null
    this.dhimportacao = null
    this.observacao = null

    this.dataagendamentocoleta = null
    this.notafiscal = null
    this.datacoleta = null
    this.datahora_followup = null
    this.vlrunitario = null
    this.totallinhaoc = null
    this.tipoorigem = new FUPLogTipoOrigem(1)
  }

  async cloneFrom (item) {
    var self = this
    self.limpardados()
    if (!item) return
    this.id = item.id
    this.followupid = item.followupid
    if (item.created_usuario) await self.created_usuario.cloneFrom(item.created_usuario)
    if (item.created_at) this.created_at = item.created_at
    if (item.datasolicitacao) this.datasolicitacao = item.datasolicitacao

    if (item.erroagendastatus) this.erroagendastatus.value = item.erroagendastatus
    if (item.erroagenda) this.erroagenda = item.erroagenda
    if (item.errocoletastatus) this.errocoletastatus.value = item.errocoletastatus
    if (item.errocoleta) this.errocoleta = item.errocoleta
    if (item.errodtpromessa) this.errodtpromessa = item.errodtpromessa
    if (item.errodtpromessastatus) this.errodtpromessastatus.value = item.errodtpromessastatus

    if (item.coletaid) this.coletaid = item.coletaid
    if (item.statusconfirmacaocoleta) this.statusconfirmacaocoleta.value = item.statusconfirmacaocoleta
    if (item.iniciofollowup) this.iniciofollowup.value = item.iniciofollowup
    if (item.ordemcompra) this.ordemcompra = item.ordemcompra
    if (item.ordemcompradig !== 'undefined') this.ordemcompradig = item.ordemcompradig
    if (item.datapromessa) this.datapromessa = item.datapromessa
    if (item.dataconfirmacao) this.dataconfirmacao = item.dataconfirmacao

    if (item.itemnumerolinhapedido) this.itemnumerolinhapedido = item.itemnumerolinhapedido
    if (item.qtdesolicitada !== 'undefined') this.qtdesolicitada = item.qtdesolicitada
    if (item.qtderecebida !== 'undefined') this.qtderecebida = item.qtderecebida
    if (item.qtdedevida !== 'undefined') this.qtdedevida = item.qtdedevida
    if (item.dhimportacao) this.dhimportacao = item.dhimportacao
    if (item.observacao) this.observacao = item.observacao
    if (item.vlrunitario !== 'undefined') this.vlrunitario = item.vlrunitario
    if (item.dataagendamentocoleta) this.dataagendamentocoleta = item.dataagendamentocoleta
    if (item.notafiscal) this.notafiscal = item.notafiscal
    if (item.datacoleta) this.datacoleta = item.datacoleta
    if (item.datahora_followup) this.datahora_followup = item.datahora_followup
    if (item.totallinhaoc) this.totallinhaoc = item.totallinhaoc
    if (typeof item.tipoorigem !== 'undefined') this.tipoorigem.value = item.tipoorigem
  }
}

export default FollowUpLog
