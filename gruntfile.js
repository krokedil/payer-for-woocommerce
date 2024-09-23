module.exports = function(grunt) {
    grunt.loadNpmTasks('grunt-wp-i18n');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-move');
    grunt.initConfig({
        makepot: {
            target: {
                options: {
                    domainPath: '/languages',
                    mainFile: 'payer-for-woocommerce',
                    potFilename: 'payer-for-woocommerce.pot',
                    processPot(pot, options) {
                        // add header options
                        return pot;
                    },
                    type: 'wp-plugin',
                },
            },
        },
        // minify css
        cssmin: {
            target: {
                files: [{
                    expand: true,
                    cwd: './assets/css',
                    src: ['*.css', '!*.min.css'],
                    dest: './assets/css',
                    ext: '.min.css',
                }],
            },
        },
    });
};