// Load plugins
var gulp = require('gulp'),
	del = require('del'),
	autoprefixer = require('gulp-autoprefixer'),
	plugins = require('gulp-load-plugins')({ camelize: true });

// Paths
var paths = {
	source: 'source/',
	assets: 'assets/',
};

// Compress SCSS files
gulp.task('sass', function() {

	return gulp.src([
		paths.source + 'sass/_variables.scss',
		paths.source + 'sass/_tweets.scss',
	], { base: paths.source + 'sass' })
	.pipe(plugins.sourcemaps.init())
	.pipe(autoprefixer({
		browsers: ['last 2 versions'],
		cascade: false
	}))
    .pipe(plugins.concat('style.scss'))
	.pipe(plugins.sass({
		outputStyle: 'compressed', // nested, expanded, compact, compressed
		sourceComments: 'normal',
	}))
	.pipe(plugins.rename('tweets.css'))
	.pipe(plugins.sourcemaps.write('sourcemaps'))
	.pipe(gulp.dest(paths.assets + 'css'));

});

// Clean
gulp.task('clean', function(cb) {
	return del([paths.assets], cb);
});

// Default task
// Build and  everyhing
gulp.task('default',['clean'], function() {
	gulp.start(
		'sass'
	);
});

// Watch
gulp.task('watch', function() {

	// Watch .scss files
	gulp.watch( paths.source + 'sass/**/*.scss', ['sass'] );

});
