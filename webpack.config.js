var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/')
    .setPublicPath('/')
    .cleanupOutputBeforeBuild()
    .autoProvidejQuery()
    .enableSassLoader()
    .enableVersioning(Encore.isProduction())
    .addEntry('app', './assets/js/app.js')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .enableIntegrityHashes(Encore.isProduction())
;

module.exports = Encore.getWebpackConfig();
