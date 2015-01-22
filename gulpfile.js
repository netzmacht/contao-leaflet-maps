var gulp       = require('gulp');
var del        = require('del');
var uglify     = require('gulp-uglify');
var concat     = require('gulp-concat');

var paths = {
    minified: 'contao-leaflet.js',
    scripts:  ['assets/maps/src/*.js'],
    dest:     'assets/maps'
};

gulp.task('clear', function(cb) {
    del([paths.dest + '/' + paths.minified], cb);
});

gulp.task('scripts', ['clear'], function() {
    return gulp.src(paths.scripts)
        .pipe(concat(paths.minified))
        .pipe(uglify())
        .pipe(gulp.dest(paths.dest));
});


gulp.task('default', ['scripts']);

gulp.task('watch', function() {
   gulp.watch(paths.scripts, ['scripts']);
});
