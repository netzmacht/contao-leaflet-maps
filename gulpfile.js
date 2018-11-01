const { series, src, watch, dest, parallel, task} = require('gulp');
const del             = require('promised-del');
const uglify          = require('gulp-uglify');
const concat          = require('gulp-concat');

var paths = {
    minified: 'contao-leaflet.js',
    scripts:  ['js/vendor/*.js', 'js/*.js'],
    dest:     'src/Bundle/Resources/public/js'
};

function clean () {
    return del([paths.dest + '/' + paths.minified]);
}

function build () {
    return src(paths.scripts)
        .pipe(concat(paths.minified))
        .pipe(uglify())
        .pipe(dest(paths.dest));
}

const buildTasks = series(clean, build);

function watchTask () {
    watch(
        paths.scripts,
        buildTasks
    )
}

exports.clean   = clean;
exports.watch   = watchTask;
exports.build   = buildTasks;
exports.default = buildTasks;
