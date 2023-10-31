const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const include = require('gulp-include');

gulp.task('sass', function () {
    return gulp.src('assets/scss/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('assets/css'));
});

gulp.task('watch', function () {
    gulp.watch('assets/scss/**/*.scss', gulp.series('sass'));
    gulp.watch('assets/js/**/*.js', gulp.series('scripts'));
});

gulp.task('scripts', function () {
    return gulp.src('assets/js/scripts.js')
        .pipe(include())
        .on('error', console.log)
        .pipe(gulp.dest('./'));
});

gulp.task('default', gulp.series('sass', 'scripts', 'watch'));
