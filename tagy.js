const path = require('path');
const replace = require('replace-in-file');

module.exports = (newVersion, oldVersion, args) => {
    replace.sync({
        files: path.resolve(__dirname, 'src/smk-sidebar-generator.php'),
        from: /Version: \d+\.\d+\.\d+/g,
        to: `Version: ${newVersion}`,
    });

    replace.sync({
        files: path.resolve(__dirname, 'src/readme.txt'),
        from: /Stable tag: \d+\.\d+\.\d+/g,
        to: `Stable tag: ${newVersion}`,
    });
}
