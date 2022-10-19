//use strict';

const gulp = require('gulp'),
  exec = require('executive'),
  argv = require('yargs').argv,
  compass = require('gulp-compass')
;

gulp.task('compass', function() {

  gulp.src(`./web/themes/pub/editor.sass`)
    .pipe(compass({
      sass: `web/themes/pub`,
      css: `web/theme/pub`,
      sourcemap: true
    }))
    .pipe(gulp.dest(`./web/themes/pub`));

});

