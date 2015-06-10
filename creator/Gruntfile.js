module.exports = function(grunt) {
    'use strict';

    require('load-grunt-tasks')(grunt);

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        connect: {
            preview: {
                options: {
                    hostname:   '<%=pkg.config.host%>',
                    port:       '<%=pkg.config.port%>',
                    base:       '.',
                    livereload: true,
                    open:       'http://<%=pkg.config.host%>:<%=pkg.config.port%>/index.html'
                }
            },
        },
        sass: {
            options: {
                sourceMap: true,
                outputStyle : 'compact',
                includePaths : ['scss']
            },
            compile: {
                files: {
                    'css/main.css': 'scss/main.scss'
                }
            }
        },
        watch : {
            preview : {
                files : ['scss/**/*.scss'],
                tasks : ['sass:compile'],
                options : {
                    livereload : true
                }
            }
        }
    });

    grunt.registerTask('preview', "opens the theme previewer", ['connect:preview', 'watch:preview']);
};

