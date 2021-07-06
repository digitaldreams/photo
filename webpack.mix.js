const mix = require('laravel-mix');
require('laravel-mix-purgecss');

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
mix.setPublicPath(path.join(__dirname));
mix.setResourceRoot(path.join(__dirname, 'resources'))
    .js('resources/js/photo.js', 'dist/js').sourceMaps()
    .extract(['popper.js', 'jquery', 'bootstrap'])
    .sass('resources/sass/photo.scss', 'dist/css').purgeCss({
    extend: {
        content: [
            path.join(__dirname, 'resources/views/**/*.php'),
        ],
        whitelistPatterns: [/hljs/],
    },
});
