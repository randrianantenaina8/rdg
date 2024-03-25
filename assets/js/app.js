/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
const $ = require('jquery');
global.$ = global.jQuery = $;
// To import bootstrap 5 Javascript, use next line.
require('bootstrap');

// start the Stimulus application
import './bootstrap';

// any CSS you import will output into a single css file (app.css in this case)
import '../styles/app.scss';

// import scripts site
import './scripts';

// import print-js library
import './print';

// import custom code for media library
import './custom';

// import logigram code
import './logigram';
