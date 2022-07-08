<template>
<div>
  <div class="fit column wrap justify-end  content-end text-caption">
      <div class="text-right  q-gutter-x-sm" v-if="createdat || createdusuario">
        <q-icon name="fas fa-plus" /> {{ createdat ? $helpers.datetimeRelativeToday(createdat) : '' }}
         {{ createdusuario ? ' por ' + $helpers.strEllipsis(createdusuario.nome, 45) : ''}}
        <q-tooltip >
          <div>Criado por  {{ createdusuario ? createdusuario.nome : '[não identificado]'}}</div>
          <div v-if="createdat">em  {{ $helpers.datetimeToBR(createdat)}}</div>
        </q-tooltip>
      </div>
      <div class="text-right  q-gutter-x-sm" v-if="(updatedat || updatedusuario) && !hideupdate">
        <q-icon name="fas fa-edit" /> {{ updatedat ? $helpers.datetimeRelativeToday(updatedat) : '' }}
        {{ updatedusuario ? ' por ' + $helpers.strEllipsis(updatedusuario.nome, 45) : '**'}}
        <q-tooltip >
          <div>Atualizado por  {{ updatedusuario ? updatedusuario.nome : '[ ** não identificado]'}}</div>
          <div v-if="updatedat">em  {{ $helpers.datetimeToBR(updatedat)}}</div>
        </q-tooltip>
      </div>
    </div>
</div>
</template>
<script>
export default {
  name: 'cpn-logusuario',
  props: [
    'createdat', 'updatedat', 'createdusuario', 'updatedusuario', 'hideupdate'
  ],
  data () {
    return {
    }
  },
  async mounted () {
  }
}
</script>
