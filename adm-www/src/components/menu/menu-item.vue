<template>
<div>
  <q-item v-if="checkpermite(item.to) && (item.menu ? item.menu.length === 0 : true)" :dense="!$q.platform.is.mobile" clickable :v-close-popup="item.to"
    :to="item.to ? item.to : null" class="text-black">
    <q-item-section avatar v-if="item.icon ? item.icon != '' : false">
      <q-icon :name="item.icon"  size="20px"/>
    </q-item-section>
    <q-item-section v-if="item.text != ''">{{item.text}}</q-item-section>
    <q-menu anchor="top right" self="top left" v-if="item.menu ? item.menu.length > 0 : false">
      <q-list :dense="!$q.platform.is.mobile" style="min-width: 100px" v-for="(link, key) in item.menu " :key="'linksubmenu' + key">
        <submenu-item :item="link" :id="'linksubmenu' + key"  />
      </q-list>
    </q-menu>
  </q-item>
  <q-expansion-item expand-separator :icon="item.icon ? item.icon : false" :label="item.text" :caption="item.caption ? item.caption : ''"
    :default-opened="false" v-if="item.menu ? item.menu.length > 0 : false" :dense="!$q.platform.is.mobile"
    class="bg-grey-3">
    <q-card class="submenucard bg-white">
      <q-card-section style="padding: 0">
        <q-list style="padding: 0">
          <div v-for="(link, key) in item.menu" :key="'key' + key">
          <q-item  v-if="checkpermite(link.to)" :dense="!$q.platform.is.mobile"  clickable :v-close-popup="link.to" :to="link.to ? link.to : null">
            <q-item-section avatar v-if="item.icon ? item.icon != '' : false">
              <q-icon color="grey" :name="link.icon" size="20px" />
            </q-item-section>
            <q-item-section v-if="link.text != ''">{{link.text}}</q-item-section>
          </q-item>
          </div>
        </q-list>
      </q-card-section>
    </q-card>
  </q-expansion-item>
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
