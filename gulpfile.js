'use strict';

var gulp       = require('gulp');
var rsync      = require('gulp-rsync');

// gulp.task('default', ['deploy']);


gulp.task('deploy-prod', [], function(){

    gulp.src('../../plugins/thermal-api')
      .pipe(rsync({
        root: '../../plugins/thermal-api',
        hostname: 'server.chaufourier.fr',
        username: 'peter',
        destination: '/home/peter/sites/admin.manganext/wp-content/plugins/thermal-api',
        recursive: true,
        emptyDirectories: true,
        incremental: true,
        progress: true,
        exclude: ['.DS_Store', '.git', '.gitignore', '.gitkeep', 'gulpfile.js', 'package.json', 'node_modules']
    }));

    return gulp.src('./')
      .pipe(rsync({
        root: './',
        hostname: 'server.chaufourier.fr',
        username: 'peter',
        destination: '/home/peter/sites/admin.manganext/wp-content/themes/manganext',
        recursive: true,
        emptyDirectories: true,
        incremental: true,
        progress: true,
        exclude: ['.DS_Store', '.git', '.gitignore', '.gitkeep', 'gulpfile.js', 'package.json', 'node_modules']
    }));
});
