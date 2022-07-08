export default {
  API_URL: process.env.DEV ? 'http://192.168.0.112/api/' : 'http://apiadministrativa.i12.com.br/api/',
  RECEITAWS_TOKEN: process.env.DEV ? '16f31c76ea7a711c13b934764198fcce00c35a7b8e3e39730ffab31f78c2384a' : '16f31c76ea7a711c13b934764198fcce00c35a7b8e3e39730ffab31f78c2384a',
  RECAPTCHA_KEY: process.env.DEV ? '6LfbL_gbAAAAALugqB68ObI15FZ7jUyc7v-_UJe5' : '6Lc5jCEcAAAAAK3ye8EbieHz4yIFzIOGoO4oIiLU',
  // pusher app coletas-webadmin-prod '480ff00fb95a9457cd19',
  // pusher app coletasapp.dev 'c63498217c12fde69e41',
  PUSHER_APP_KEY: process.env.DEV ? 'c63498217c12fde69e41' : '480ff00fb95a9457cd19',
  PUSHER_CLUSTER: 'us2'
}
