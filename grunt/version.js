/* jshint node:true */
//https://github.com/kswedberg/grunt-version
module.exports = {
    options: {
        pkg: {
            version: '<%= package.version %>'
        }
    },
    project: {
        src: [
            'package.json'
        ]
    },
    style: {
        options: {
            prefix: 'Version\\:\\s'
        },
        src: [
            'adblock-notify.php',
            'css/an-style.css',
        ]
    },
    functions: {
        options: {
            prefix: 'AN_VERSION\'\,\\s+\''
        },
        src: [
            'adblock-notify.php',
        ]
    }
};