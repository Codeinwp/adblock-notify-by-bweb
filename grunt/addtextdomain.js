/* jshint node:true */
// https://github.com/blazersix/grunt-wp-i18n
module.exports = {
    options: {
        textdomain: '<%= pkg.plugin.textdomain %>',
    },
    add: {
        files: {
            src: [
                '<%= files.php %>'
            ],
        },
    },
};