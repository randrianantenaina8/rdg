const Encore = require('@symfony/webpack-encore');
const CopyWebpackPlugin = require('copy-webpack-plugin'); // this line tell to webpack to use the plugin
const dotenv = require('dotenv');
// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/js/app.js')
    .addEntry('admin', './assets/js/admin.js')
    .addEntry('bo_centermap', './assets/js/bo_centermap.js')
    .addEntry('custom', './assets/js/custom.js')
    .addEntry('jquery', './assets/js/jquery.js')
    .addEntry('jquery-ui', './assets/js/jquery-ui.js')
    .addEntry('logigram', './assets/js/logigram.js')
    
    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)


    .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

    // define the environment variables
    .configureDefinePlugin(options => {
        const env = dotenv.config();

        if (env.error) {
            throw env.error;
        }

        options['process.env'].S3_ENDPOINT = JSON.stringify(env.parsed.S3_ENDPOINT);
        options['process.env'].S3_BUCKET = JSON.stringify(env.parsed.S3_BUCKET);
    })

    .addPlugin(new CopyWebpackPlugin({
        'patterns': [
            { from: './assets/images', to: 'images' },
            { from: './assets/styles/css', to: 'css' },
            { from: './node_modules/@gouvfr/dsfr/dist', to: 'dsfr'},
            { from: './assets/tarteaucitron', to: 'tarteaucitron' },
            // ckeditor Plugins
            { from: './assets/ckeditor/contents', to: 'ckeditor/extra-plugins/contents/[path][name][ext]'},
            { from: './assets/ckeditor/dropoff', to: 'ckeditor/extra-plugins/dropoff/[path][name][ext]'},
            { from: './assets/ckeditor/table2', to: 'ckeditor/extra-plugins/table2/[path][name][ext]'},
            { from: './assets/ckeditor/dialog', to: 'ckeditor/extra-plugins/dialog/[path][name][ext]'},
            { from: './assets/ckeditor/image2', to: 'ckeditor/extra-plugins/image2/[path][name][ext]'},
            { from: './assets/ckeditor/widget', to: 'ckeditor/extra-plugins/widget/[path][name][ext]'},
            { from: './assets/ckeditor/logigram', to: 'ckeditor/extra-plugins/logigram/[path][name][ext]'},
            { from: './assets/ckeditor/comments', to: 'ckeditor/extra-plugins/comments/[path][name][ext]'},
         ]
    }))
;

//module.exports = Encore.getWebpackConfig();

let config = Encore.getWebpackConfig();

config.externals.jquery = 'jQuery';
module.exports = config;