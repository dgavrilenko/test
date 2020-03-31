const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

/**
 * компилим с ThreadPool в несколько потоков (оптимизация сборки)
 */
const HappyPack = require('happypack');
const happyThreadPool = HappyPack.ThreadPool({ size: 4 });

/**
 * https://github.com/mzgoddard/hard-source-webpack-plugin
 * добавляем промежуточное кеширование (оптимизация сборки, для быстрой повторной компиляции)
 */
const HardSourceWebpackPlugin = require('hard-source-webpack-plugin');


mix.browserSync('alt');

const webpackConfig = {};
webpackConfig.plugins = [];

webpackConfig.plugins.push(new HardSourceWebpackPlugin());
webpackConfig.plugins.push(
    new HappyPack({
        id: 'js',
        threadPool: happyThreadPool,
        loaders: [
            {
                test: /\.js$/,
                loader: 'babel-loader',
                query: {
                    presets: ['es2015'], // or whatever
                    plugins: [
                        'babel-plugin-transform-class-properties',
                        '@babel/plugin-syntax-dynamic-import',
                        '@babel/plugin-transform-arrow-functions'
                    ],
                    compact: false
                }
            }
        ]
    })
);

webpackConfig.plugins.push(
    new HappyPack({
        id: 'styles',
        threadPool: happyThreadPool,
        loaders: ['style-loader', 'css-loader', 'sass-loader']
    })
);

mix
    .webpackConfig(webpackConfig)
    .setPublicPath('public/build')
    .setResourceRoot('/build/')
    .js('resources/js/app.js', 'js')
    //.extract()
    .sass('resources/sass/app.scss', 'css');
