var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/')
    .setPublicPath('/public')
    .cleanupOutputBeforeBuild()
    .autoProvidejQuery()
    .enableSassLoader()
    // when versioning is enabled, each filename will include a hash that changes
    // whenever the contents of that file change. This allows you to use aggressive
    // caching strategies. Use Encore.isProduction() to enable it only for production.
    .enableVersioning(false)
    .addEntry('/js/app', './assets/js/app.js')
    .addStyleEntry('/css/style', './assets/scss/style.scss')
    .addStyleEntry('/css/colors/megna-dark', './assets/scss/colors/megna-dark.scss')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .enableIntegrityHashes(Encore.isProduction())
;

module.exports = Encore.getWebpackConfig();
