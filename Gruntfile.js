/* jshint node:true */
/* global require */
var timeGrunt = require('time-grunt');

module.exports = function (grunt) {
    'use strict';

    timeGrunt(grunt);
    var loader = require( 'load-project-config' ),
        config = require( 'grunt-plugin-fleet' );
    loader( grunt, config ).init();
};