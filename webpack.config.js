const Encore = require('@symfony/webpack-encore');
const path = require('path');
const getEzConfig = require('./ez.webpack.config.js');
const eZConfigManager = require('./ez.webpack.config.manager.js');
const eZConfig = getEzConfig(Encore);
const customConfigs = require('./ez.webpack.custom.configs.js');

Encore.reset();
Encore.setOutputPath('public/assets/build')
    .setPublicPath('/assets/build')
    .enableSassLoader()
    .enableReactPreset()
    .enableSingleRuntimeChunk()
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[ext]',
        pattern: /\.(png|svg)$/
    });

// Welcome page stylesheets
Encore.addEntry('welcome_page', [
    path.resolve(__dirname, './assets/scss/welcome-page.scss'),
]);


Encore.addEntry('content', [
    path.resolve(__dirname, './assets/css/app.css'),
    path.resolve(__dirname, './assets/css/content.css'),
    path.resolve(__dirname, './assets/css/print.css')
]);

Encore.addEntry('app', [
    path.resolve(__dirname, './assets/css/app.css'),
]);

// uncomment the two lines below, if you added a new entry (by Encore.addEntry() or Encore.addStyleEntry() method) to your own Encore configuration for your project
const projectConfig = Encore.getWebpackConfig();
module.exports = [ eZConfig, ...customConfigs, projectConfig ];

