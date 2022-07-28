export default {
  API_URL: process.env.DEV ? 'http://192.168.0.112/api/' : 'https://apiadministrativa.i12.com.br/api/',
  RECAPTCHA_KEY: process.env.DEV ? '6Lds6QshAAAAAKHp8yfwh69Jrqd_nClifi9lm2sl' : '6LcKBishAAAAAFMF3mulqx1R9fvVyANyjgQuey_E',
  ABSTRACTAPI: process.env.DEV ? '75c729724af64d6daf867c455ec8e8c8' : '75c729724af64d6daf867c455ec8e8c8',
  // RECEITAWS_TOKEN: process.env.DEV ? '16f31c76ea7a711c13b934764198fcce00c35a7b8e3e39730ffab31f78c2384a' : '16f31c76ea7a711c13b934764198fcce00c35a7b8e3e39730ffab31f78c2384a',
  // pusher app coletas-webadmin-prod '480ff00fb95a9457cd19',
  // pusher app coletasapp.dev 'c63498217c12fde69e41',
  PUSHER_APP_KEY: process.env.DEV ? 'c63498217c12fde69e41' : '480ff00fb95a9457cd19',
  PUSHER_CLUSTER: 'us2'
}
