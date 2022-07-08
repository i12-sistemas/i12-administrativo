// Configuration for your app
// https://quasar.dev/quasar-cli/quasar-conf-js
// eslint-disable-next-line no-unused-vars
const fs = require('fs')

module.exports = function (ctx) {
  return {
    // app boot file (/src/boot)
    // --> boot files are part of "main.js"
    // https://quasar.dev/quasar-cli/cli-documentation/boot-files
    boot: [
      'i18n',
      'config',
      'eventbus',
      '/helpers',
      'pusher',
      'axios',
      'apexcharts',
      'register-my-component',
      'prepareapp'
    ],

    // https://quasar.dev/quasar-cli/quasar-conf-js#Property%3A-css
    css: [
      'app.scss',
      'app.css'
    ],

    // https://github.com/quasarframework/quasar/tree/dev/extras
    extras: [
      'ionicons-v4',
      'mdi-v4',
      'fontawesome-v5',
      'eva-icons',
      'themify',
      // 'roboto-font-latin-ext', // this or either 'roboto-font', NEVER both!

      'roboto-font',
      'material-icons'
    ],

    // https://quasar.dev/quasar-cli/quasar-conf-js#Property%3A-framework
    framework: {
      config: {
        dark: false
      },
      // iconSet: 'ionicons-v4', // Quasar icon set
      lang: 'pt-br', // Quasar language pack

      // Possible values for "all":
      // * 'auto' - Auto-import needed Quasar componentsfalse
      //            (slightly higher compile time; next to minimum bundle size; most convenient)
      // * false  - Manually specify what to import
      //            (fastest compile time; minimum bundle size; most tedious)
      // * true   - Import everything from Quasar
      //            (not treeshaking Quasar; biggest bundle size; convenient)
      all: 'auto',

      components: [
        'QList',
        'QItem',
        'QItemSection',
        'QItemLabel',
        'QPage',
        'QPageContainer',
        'QInnerLoading',
        'QSpinnerTail',
        'QEditor',
        'QTooltip',
        'QIcon',
        'QCard',
        'QCardSection',
        'QCardActions',
        'QBtnDropdown',
        'QMenu',
        'QCarousel',
        'QCarouselControl',
        'QCarouselSlide',
        'QBtnGroup',
        'QSlideTransition',
        'QCheckbox'
      ],
      directives: [
        'Ripple',
        'ClosePopup',
        'TouchHold'
      ],

      // Quasar plugins
      plugins: [
        'Notify',
        'Dialog',
        'BottomSheet',
        'Meta'
      ]
    },

    // https://quasar.dev/quasar-cli/cli-documentation/supporting-ie
    supportIE: true,

    // https://quasar.dev/quasar-cli/quasar-conf-js#Property%3A-build
    build: {
      scopeHoisting: true,
      vueRouterMode: 'history',
      showProgress: true,
      // gzip: true,
      // analyze: true,
      // preloadChunks: false,
      // extractCSS: false,

      // https://quasar.dev/quasar-cli/cli-documentation/handling-webpack
      extendWebpack (cfg) {
        cfg.module.rules.push({
          enforce: 'pre',
          test: /\.(js|vue)$/,
          loader: 'eslint-loader',
          exclude: /node_modules/,
          options: {
            formatter: require('eslint').CLIEngine.getFormatter('stylish')
          }
        })
      }
    },

    // https://quasar.dev/quasar-cli/quasar-conf-js#Property%3A-devServer
    devServer: {
      port: 8089,
      // https: {
      //   key: fs.readFileSync('.cert/localhost-key.pem'),
      //   cert: fs.readFileSync('.cert/localhost-cert.pem')
      // //   ca: fs.readFileSync('/path/to/ca.pem')
      // },
      before (app) {
        const cors = require('cors')
        app.options('*', cors())
        app.use(cors())
      },
      headers: {
        // 'Access-Control-Allow-Methods': 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
        // 'Access-Control-Allow-Headers': 'Content-Type, Authorization',
        'Access-Control-Allow-Origin': '*'
      }
    },

    animations: 'all', // --- includes all animations
    // https://quasar.dev/options/animations
    // animations: [],

    // https://quasar.dev/quasar-cli/developing-ssr/configuring-ssr
    ssr: {
      pwa: false
    },

    // https://quasar.dev/quasar-cli/developing-pwa/configuring-pwa
    pwa: {
      // workboxPluginMode: 'InjectManifest',
      // workboxOptions: {}, // only for NON InjectManifest
      manifest: {
        name: 'Portal Administrativo - i12 Sistemas',
        short_name: 'portaladmwebapp',
        // description: 'A Quasar Framework app',
        display: 'standalone',
        orientation: 'portrait',
        background_color: '#ffffff',
        theme_color: '#1976d2',
        icons: [
          {
            'src': 'statics/icons/icon-128x128.png',
            'sizes': '128x128',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-192x192.png',
            'sizes': '192x192',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-256x256.png',
            'sizes': '256x256',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-384x384.png',
            'sizes': '384x384',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-512x512.png',
            'sizes': '512x512',
            'type': 'image/png'
          }
        ]
      }
    },

    // https://quasar.dev/quasar-cli/developing-cordova-apps/configuring-cordova
    cordova: {
      // id: 'org.cordova.quasar.app',
      // noIosLegacyBuildFlag: true, // uncomment only if you know what you are doing
    },

    // https://quasar.dev/quasar-cli/developing-electron-apps/configuring-electron
    electron: {
      bundler: 'builder', // or 'packager'

      extendWebpack (cfg) {
        // do something with Electron main process Webpack cfg
        // chainWebpack also available besides this extendWebpack
      },

      packager: {
        // https://github.com/electron-userland/electron-packager/blob/master/docs/api.md#options

        // OS X / Mac App Store
        // appBundleId: '',
        // appCategoryType: '',
        // osxSign: '',
        // protocol: 'myapp://path',

        // Windows only
        // win32metadata: { ... }
      },

      builder: {
        // https://www.electron.build/configuration/configuration

        // appId: 'vendedorapp'
      }
    }
  }
}
