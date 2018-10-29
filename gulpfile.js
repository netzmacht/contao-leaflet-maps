const { series, src, watch, dest} = require('gulp');
const del             = require('del');
const uglify          = require('gulp-uglify');
const concat          = require('gulp-concat');

var paths = {
    minified: 'contao-leaflet.js',
    scripts:  ['js/*.js'],
    dest:     'src/Bundle/Resources/public/js'
};

function cleanTask (cb) {
    del([paths.dest + '/' + paths.minified]);
    cb();
}

const buildTask = series(cleanTask, function (cb) {
    return src(paths.scripts)
        .pipe(concat(paths.minified))
        .pipe(uglify())
        .pipe(dest(paths.dest));
});

exports.clean   = cleanTask;
exports.watch   = watch(paths.scripts, buildTask);
exports.build   = buildTask;
exports.default = buildTask;
