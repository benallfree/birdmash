//░░░░░░░░░░░░░░░░░░░░░░░░
//
//	 DIRECTORY
//
//	 _Requires
//	 _Configuration
//	 _CSS
//	 _JS
//	 _BrowserSync
//	 _Watch
//	 _Clean
//	 _Build
//	 _Default
//	 _SwallowError
//
//░░░░░░░░░░░░░░░░░░░░░░░░

//----------------------------------------------
// _Requires
//----------------------------------------------
var gulp        = require( 'gulp' ),
	concat      = require( 'gulp-concat' ),
	uglify      = require( 'gulp-uglify' ),
	rename      = require( 'gulp-rename' ),
	sass        = require( 'gulp-sass' ),
	sassGlob    = require( 'gulp-sass-glob' ),
	maps        = require( 'gulp-sourcemaps' ),
	browserSync = require( 'browser-sync' ).create(),
	del         = require( 'del' ),
	cssNano     = require( 'gulp-cssnano' ),
	prefixer    = require( 'gulp-autoprefixer' ),
	dom         = require( 'gulp-dom' );

	// CONFIG
	try {
		var	personalConfig = require( './gulp-config' );
	} catch (e) {
		if (e instanceof Error && e.code === "MODULE_NOT_FOUND") {
			console.log(e.code);
			var personalConfig = { browserSync : '' };
		} else {
			throw e;
		}
	}


//----------------------------------------------
// _Configuration
//----------------------------------------------
var config = {
		'css' : {
			'origin'    : 'scss',
			'dest'      : 'assets/css',
			'getFiles'  : '*',
			'fileName'  : 'styles',
			'supportSrc': []
		},
		'js' : {
			'origin'   : 'js',
			'dest'     : 'assets/js',
			'getFiles' : '**/*',
			'fileName' : 'functions',
			'src'      : []
		},
		'browserSync' : personalConfig.browserSync !== '' ? personalConfig.browserSync : {

		}
	}

//----------------------------------------------
// _CSS
//----------------------------------------------
gulp.task( 'compileSass', function (){

	return gulp.src( config.css.origin + '/' + config.css.getFiles + '.scss' )
		.pipe( maps.init() )
		.pipe( sassGlob() )
		.pipe( sass() )
		.on( 'error', swallowError )
		.pipe( cssNano() )
		.pipe( rename( { suffix : '.min' } ) )
		.pipe( prefixer({
			browsers: ['last 5 versions'],
			remove: false
		}))
		.pipe( maps.write( './' ) )
		.pipe( gulp.dest( config.css.dest ) )
		.pipe( browserSync.reload( { stream: true })
	);

});

gulp.task( 'supportCss', function (){
	if ( config.css.supportSrc.length > 0 ) {

		return gulp.src( config.css.supportSrc )
			.pipe( maps.init() )
			.pipe( concat( 'support.css' ) )
			.pipe( maps.write( './' ) )
			.pipe( gulp.dest( config.css.dest ) );

	}
});


//----------------------------------------------
// _JS
//----------------------------------------------
gulp.task( 'jsMagic', function (){

	return gulp.src( config.js.origin + '/' + config.js.getFiles + '.js' )
		.pipe( maps.init() )
		.pipe( concat( config.js.fileName + '.min.js' ) )
		.pipe( uglify() )
		.on('error', swallowError)
		.pipe( maps.write( './' ) )
		.pipe( gulp.dest( config.js.dest ) )
		.pipe( browserSync.reload( { stream: true }) );

});


//----------------------------------------------
// _BrowserSync
//----------------------------------------------
gulp.task( 'browserSync', function (){

	browserSync.init( config.browserSync );

});

gulp.task( 'generalRefresh', function (){

	browserSync.reload();

});


//----------------------------------------------
// _Watch
//----------------------------------------------
gulp.task( 'watchFiles', [ 'compileSass', 'supportCss', 'jsMagic', 'browserSync' ], function (){

	gulp.watch( 'scss/**/*.scss', ['compileSass'] );
	gulp.watch( 'js/*.js', ['jsMagic'] );
	gulp.watch( '**/*.php', ['generalRefresh'] );

});


//----------------------------------------------
// _Clean
//----------------------------------------------
gulp.task( 'clean', function (){

	del( [ config.css.dest + '/**', '!' + config.css.dest, config.js.dest + '/**', '!' + config.js.dest ] );

});


//----------------------------------------------
// _Build
//----------------------------------------------
gulp.task( 'build',  [ 'clean', 'compileSass', 'supportCss', 'jsMagic' ], function (){

	console.log( 'We built this city on rock and roll' );

});


//----------------------------------------------
// _Default
//----------------------------------------------
gulp.task( 'default', ['watchFiles']);


//----------------------------------------------
// _SwallowError
//----------------------------------------------
function swallowError (error) {

  // If you want details of the error in the console
  console.log( error.toString() );

  this.emit('end');

}
