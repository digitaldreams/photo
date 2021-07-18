try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    window.bootstrap = require('bootstrap/dist/js/bootstrap.bundle');
} catch (e) {
}
