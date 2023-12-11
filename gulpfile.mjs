import gulp from 'gulp';
import * as sass from 'sass';
import gulpSass from 'gulp-sass';
const gulpSassCompiler = gulpSass(sass);
import include from 'gulp-include';
import npmDist from 'gulp-npm-dist';
import zip from 'gulp-zip';

gulp.task('sass', function () {
    return gulp.src('assets/scss/**/*.scss')
        .pipe(gulpSassCompiler().on('error', gulpSassCompiler.logError))
        .pipe(gulp.dest('assets/css'));
});

gulp.task('watch', function () {
    gulp.watch('assets/scss/**/*.scss', gulp.series('sass'));
    gulp.watch('assets/js/**/*.js', gulp.series('scripts'));
    gulp.watch('node_modules/**/*', gulp.series('copy-dependencies'));
});

gulp.task('copy-dependencies', function () {
    return gulp.src(npmDist(), { base: './node_modules' })
        .pipe(gulp.dest('./assets/dependencies'));
});

gulp.task('zip', function () {
    return gulp.src([
        './**',
        '!./node_modules',
        '!./node_modules/**',
        '!./dist',
        '!./dist/**',
        '!./assets/scss',
        '!./assets/scss/**',
        '!./assets/js',
        '!./assets/js/**',
        '!./gulpfile.mjs',
        '!./package.json',
        '!./package-lock.json',
        '!./.gitignore',
        '!./.git',
        '!./.git/**',
        '!./.vscode',
        '!./.vscode/**',
        '!./dist/',
        '!./dist/**',
    ])
        .pipe(zip('archive.zip'))
        .pipe(gulp.dest('dist'));
});

gulp.task('scripts', function () {
    return gulp.src('assets/js/scripts.js')
        .pipe(include())
        .on('error', console.log)
        .pipe(gulp.dest('./'));
});

gulp.task('default', gulp.series('sass', 'scripts', 'watch'));
