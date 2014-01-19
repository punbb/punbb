module.exports = function (grunt) {
    "use strict";

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        uglify: {
            options: {
                mangle: true
            },
            build: {
                files:
                {
                    'include/js/min/punbb.common.min.js': [
                        'include/js/LAB.src.js',
                        'include/js/punbb.common.js'
                    ],

                    'include/js/min/punbb.timezone.min.js': [
                        'include/js/punbb.timezone.js'
                    ],

                    'include/js/min/punbb.install.min.js': [
                        'include/js/punbb.install.js'
                    ]
                }
            }
        },

        cssmin: {
            combine: {
                options: {
                    keepSpecialComments: 0,
                    report: 'min'
                },

                files:
                {
                    'style/Oxygen/Oxygen.min.css': [
                        'style/Oxygen/Oxygen.css',
                        'style/Oxygen/Oxygen_cs.css'
                    ]
                }
            }
        },

        watch: {
            javascripts: {
                files: ['include/js/**/*.js'],
                tasks: ['uglify'],
                options: {
                    spawn: false
                },
            },

            stylesheets: {
                files: ['style/Oxygen/**/*.css'],
                tasks: ['cssmin'],
                options: {
                    spawn: false
                },
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', ['uglify', 'cssmin']);
};
