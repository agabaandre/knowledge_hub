var path = require('path');
module.exports = {
    entry: ['./jquery.blockui.js'],
    output: {
        path: path.join(__dirname, './'),
        filename: 'jquery.blockui.min.js'
    }
};
