// Include gulp
var gulp = require('gulp');

// Include Our Plugins
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var cssnano = require('gulp-cssnano');
var plumber = require('gulp-plumber');

var styleName = 'public.css';
var scriptName = 'public.js';

// Compile Our Sass
gulp.task('sass', function() {
    return gulp.src('src/scss/*.scss')
        .pipe(plumber())
        .pipe(sass())
        .pipe(concat(styleName))
        .pipe(rename({suffix: '.min'}))
        .pipe(cssnano(
            {
                discardComments: {removeAll: true}
            }
        ))
        .pipe(gulp.dest('css/'))
});


// Concatenate & Minify JS
gulp.task('scripts', function() {
    return gulp.src('src/scripts/*.js')
        .pipe(plumber())
        .pipe(concat(scriptName))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('js/'));
});

// Watch Files For Changes
gulp.task('watch', function() {
    gulp.watch('src/scss/**/*.scss', ['sass']);
    gulp.watch('src/scripts/*.js', ['scripts']);

});

// Default Task
gulp.task('default', ['sass', 'watch']);