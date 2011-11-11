;(function(core, $, window, undefined) {

    /** init namespace */
    var util = core.namespace('core.util');

    /** run module */
    util.run = function() {
        if (!core['module']) {
            return false;
        }

        var root = core['module']
          , module
          , i
          , l;

        for (i = 0, l = arguments.length; i < l; i++) {
            module = arguments[i];
            if (!!root[module]) {
                if (typeof root[module]['init'] === 'function') {
                    root[module]['init']();
                    root = root[module];
                }
            } else {
                break;
            }
        }
    };

    /** finalize */
    util.finalize = function() {
        $(document).trigger('finalized');
    };

    /** bind to finalized */
    util.bindFinalized = function(fn) {
        $(document).bind('finalized', fn);
    };

    /** init current module */
    util.init = function() {
        var module = $('body').attr('class').split(' ');
        util.run('base');
        util.run.apply(util, module);
        util.finalize();
    };

    /** init */
    $(util.init);
})(core, jQuery, window);