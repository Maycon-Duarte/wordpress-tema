import gulp from 'gulp';
import * as sass from 'sass';
import gulpSass from 'gulp-sass';
import include from 'gulp-include';
import npmMainfiles from 'gulp-npm-mainfiles';
import zip from 'gulp-zip';
import browserSync from 'browser-sync';

const bs = browserSync.create();
const gulpSassCompiler = gulpSass(sass);

gulp.task('sass', function () {
    return gulp.src('assets/scss/**/*.scss')
        .pipe(gulpSassCompiler().on('error', gulpSassCompiler.logError))
        .pipe(gulp.dest('assets/css'))
        .pipe(bs.stream());
});

gulp.task('watch', function () {
    gulp.watch('assets/scss/**/*.scss', gulp.series('sass'));
    gulp.watch('assets/js/**/*.js', gulp.series('scripts'));
    gulp.watch('package.json', gulp.series('copy-dependencies'));
});

gulp.task('copy-dependencies', function (done) {
    gulp.src(npmMainfiles(), { base: "./node_modules" })
        .pipe(gulp.dest('./assets/lib'))
        .on('end', done);  // Adicionando o callback done para indicar conclusão assíncrona
});

gulp.task('serve', function () {
    bs.init({
        proxy: "solicitacoes-taesa.local", // Configurando proxy para o servidor PHP
        notify: false
    });

    gulp.series('watch')();

    gulp.watch('**/*.php').on('change', bs.reload);
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
        .pipe(gulp.dest('./'))
        .pipe(bs.stream());
});

gulp.task('default', gulp.series('copy-dependencies', 'sass', 'scripts', 'watch'));