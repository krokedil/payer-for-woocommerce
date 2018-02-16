var gulp     = require('gulp');
var wpPot    = require('gulp-wp-pot');
var sort     = require('gulp-sort');

gulp.task('makePOT', function () {
    return gulp.src('**/*.php')
        .pipe(sort())
		.pipe(wpPot({
			domain: 'payer-for-woocommerce',
			package: 'payer-for-woocommerce',
			bugReport: 'http://krokedil.se',
			lastTranslator: 'Michael Bengtsson <michael@krokedil.se>',
			team: 'Krokedil <info@krokedil.se>'
        }))
        .pipe(gulp.dest('languages/payer-for-woocommerce.pot'))
});

gulp.task('build', ['makePOT']);