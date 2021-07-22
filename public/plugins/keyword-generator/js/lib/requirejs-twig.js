/* globals define, requirejs, process */
define(['module', 'twig'], function(module, Twig) {
    'use strict';

    var twig = Twig.twig,
        moduleConfig = (module && module.config ? module.config() : {}),
        TWIG_COMPILER_MODULE = 'requirejs-twig';

    function defaults(source, defaults) {
        var key;

        for (key in defaults) {
            if (defaults.hasOwnProperty(key) && typeof source[key] === 'undefined') {
                source[key] = defaults[key];
            }
        }

        return source;
    }

    defaults(moduleConfig, {
        autoescape: true,
        extension: 'twig'
    });

    Twig.extend(function(Twig) {
        var compiler = Twig.compiler;

        compiler.module[TWIG_COMPILER_MODULE] = function(id, tokens, pathToTwig) {
            var output = [
                    'define(["' + pathToTwig + '"], function(Twig) {',
                    '    var twig = Twig.twig,',
                    '        template = ' + compiler.wrap(id, tokens),
                    '    template.options.autoescape = ' + moduleConfig.autoescape + ';\n',
                    '    return function(context) { return template.render(context); };',
                    '});\n'
                ];

            return output.join('\n');
        };
    });

    module.exports = {

        /**
         * Adds a file extension to the module.
         * @param {String} name The name of the resource.
         * @param {String} extension The extension of the resource.
         * @returns {String}
         */
        applyExtension: function(name, extension) {
            if (typeof extension === 'string' && extension.length !== 0) {
                return name + '.' + extension;
            }

            return name;
        },

        /**
         * @see {@link http://requirejs.org/docs/plugins.html#apiload}
         * @param {String} name The name of the resource to load.
         * @param {Function} parentRequire A local "require" function to use to load other modules.
         * @param {Function} onLoad A function to call with the value for name.
         */
        load: function(name, parentRequire, onLoad) {
            var url = parentRequire.toUrl(this.applyExtension(name, moduleConfig.extension));

            twig({
                id: name,
                href: url,
                autoescape: moduleConfig.autoescape,
                load: function(template) {
                    var renderer = function(context) {
                            return template.render(context);
                        };

                    renderer.source = template;
                    onLoad(renderer);
                },
                error: onLoad.error
            });
        },

        /**
         * A function to be called with a string of output to write to the optimized file.
         * @see {@link http://requirejs.org/docs/plugins.html#apiwrite}
         * @param {String} pluginName The normalized name for the plugin.
         * @param {String} moduleName The normalized resource name.
         * @param {Function} write
         */
        write: function(pluginName, moduleName, write) {
            var template = twig({ ref: moduleName });

            if (template) {
                write.asModule(pluginName + '!' + moduleName, template.compile({
                    module: TWIG_COMPILER_MODULE,
                    twig: 'twig'
                }));
            }
        }

    };

    // if node environment
    if (typeof process !== 'undefined' &&
        process.versions &&
        !!process.versions.node &&
        !process.versions['node-webkit'] &&
        !process.versions['atom-shell']) {

        (function() {
            var fs = requirejs.nodeRequire('fs');

            /**
             * @param {String} name
             * @param {Function} parentRequire
             * @param {Function} onLoad
             */
            module.exports.load = function(name, parentRequire, onLoad) {
                var url = parentRequire.toUrl(this.applyExtension(name, moduleConfig.extension));

                try {
                    onLoad(twig({
                        id: name,
                        data: fs.readFileSync(url, 'utf-8')
                    }));
                } catch(e) {
                    onLoad.error(e);
                }
            };

        }());
    }

});
