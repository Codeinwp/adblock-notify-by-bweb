/* jshint node:true */
// https://github.com/mducharme/grunt-phpcbf

module.exports = {
    run: {
        options: {},
        files: {
            src:['<%= files.php %>']
        }
    }
};