<template>
<div>
  <q-item v-close-popup :dense="!$q.platform.is.mobile" clickable :v-close-popup="item.to"
    :to="item.to ? item.to : null" v-if="checkpermite(item.to) && (item.menu ? item.menu.length === 0 : true)" class="text-black">
    <q-item-section v-if="item.text != ''">{{item.text}}</q-item-section>
    <q-item-section avatar v-if="item.icon ? item.icon != '' : false">
      <q-icon color="grey" :name="item.icon" size="20px" />
    </q-item-section>
  </q-item>
  <q-item clickable v-if="(item.menu ? item.menu.length > 0 : false)" :dense="!$q.platform.is.mobile">
    <q-item-section>{{item.text}}</q-item-section>
    <q-item-section side>
      <q-icon name="keyboard_arrow_right" />
    </q-item-section>
      <q-menu anchor="top right" self="top left"  >
        <q-list>
          <div v-for="(link, key) in item.menu" :key="'key' + key">
          <q-item v-if="checkpermite(link.to)" :dense="!$q.platform.is.mobile"  clickable :v-close-popup="link.to" :to="link.to ? link.to : null">
            <q-item-section avatar v-if="item.icon ? item.icon != '' : false">
              <q-icon color="grey" :name="link.icon" size="20px" />
            </q-item-section>
            <q-item-section v-if="link.text != ''">{{link.text}}</q-item-section>
          </q-item>
          </div>
        </q-list>
      </q-menu>
    </q-item>
</div>
</template>

<script>
export default {
  name: 'menu-item',
  components: {
  },
  props: ['item'],
  data () {
    return {
    }
  },
  async mounted () {
  },
  methods: {
    checkpermite (toName) {
      try {
        var app = this
        var permite = true
        if (!toName) throw new Error('0')
        var route = app.$router.resolve({ name: toName.name })
        if (!route) throw new Error('1')
        if (!route) throw new Error('1')
        if (!route.route) throw new Error('2')
        if (!route.route.meta) throw new Error('4')
        if (route.route.name !== toName.name) throw new Error('3')
        if (!route.route.meta.permissao) throw new Error('5')
        var check = app.$helpers.permite(route.route.meta.permissao)
        permite = check.ok
      } catch (error) {
        // permite = false
      }
      return permite
    }
  }
}
</script>
<style lang="stylus">
.submenucard {
  padding: 0px 0px 0px 10px;
  border-left: 5px solid #f05b41;
  border-top: 1px solid #dbdbdb;
  border-bottom: 1px solid #dbdbdb;
}
</style>
