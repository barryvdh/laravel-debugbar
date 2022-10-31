(function($) {

    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for the displaying templates data
     *
     * Options:
     *  - data
     */
    var TemplatesWidget = PhpDebugBar.Widgets.LaravelViewTemplatesWidget = PhpDebugBar.Widget.extend({

        className: csscls('templates'),

        render: function() {
            this.$status = $('<div />').addClass(csscls('status')).appendTo(this.$el);

            this.$list = new  PhpDebugBar.Widgets.ListWidget({ itemRenderer: function(li, tpl) {
                $('<span />').addClass(csscls('name')).text(tpl.name).appendTo(li);

                if (typeof tpl.editorLink !== 'undefined' && tpl.editorLink !== null) {
                    $('<a href="' + tpl.editorLink + '"></a>')
                        .addClass(csscls('editor-link'))
                        .on('click', function (event) {
                            event.stopPropagation();
                        })
                        .appendTo(li);
                }
                if (typeof tpl.xdebug_link !== 'undefined' && tpl.xdebug_link !== null) {
                    if (tpl.xdebug_link.ajax) {
                        $('<a title="' + tpl.xdebug_link.url + '"></a>').on('click', function () {
                            $.ajax(tpl.xdebug_link.url);
                        }).addClass(csscls('editor-link')).appendTo(li);
                    } else {
                        $('<a href="' + tpl.xdebug_link.url + '"></a>').addClass(csscls('editor-link')).appendTo(li);
                    }
                }
                if (tpl.render_time_str) {
                    $('<span title="Render time" />').addClass(csscls('render-time')).text(tpl.render_time_str).appendTo(li);
                }
                if (tpl.memory_str) {
                    $('<span title="Memory usage" />').addClass(csscls('memory')).text(tpl.memory_str).appendTo(li);
                }
                if (typeof(tpl.param_count) != 'undefined') {
                    $('<span title="Parameter count" />').addClass(csscls('param-count')).text(tpl.param_count).appendTo(li);
                }
                if (typeof(tpl.type) != 'undefined' && tpl.type) {
                    $('<span title="Type" />').addClass(csscls('type')).text(tpl.type).appendTo(li);
                }
                if (tpl.params && !$.isEmptyObject(tpl.params)) {
                    var table = $('<table><tr><th colspan="2">Params</th></tr></table>').addClass(csscls('params')).appendTo(li);
                    for (var key in tpl.params) {
                        if (typeof tpl.params[key] !== 'function') {
                            table.append('<tr><td class="' + csscls('name') + '">' + key + '</td><td class="' + csscls('value') +
                            '"><pre><code>' + tpl.params[key] + '</code></pre></td></tr>');
                        }
                    }
                    li.css('cursor', 'pointer').click(function() {
                        if (table.is(':visible')) {
                            table.hide();
                        } else {
                            table.show();
                        }
                    });
                }
            }});
            this.$list.$el.appendTo(this.$el);
            this.$callgraph = $('<div />').addClass(csscls('callgraph')).appendTo(this.$el);

            this.bindAttr('data', function(data) {
                this.$list.set('data', data.templates);
                this.$status.empty();
                this.$callgraph.empty();

                var sentence = data.sentence || "templates were rendered";
                $('<span />').text(data.nb_templates + " " + sentence).appendTo(this.$status);

                if (data.accumulated_render_time_str) {
                    this.$status.append($('<span title="Accumulated render time" />').addClass(csscls('render-time')).text(data.accumulated_render_time_str));
                }
                if (data.memory_usage_str) {
                    this.$status.append($('<span title="Memory usage" />').addClass(csscls('memory')).text(data.memory_usage_str));
                }
                if (data.nb_blocks > 0) {
                    $('<div />').text(data.nb_blocks + " blocks were rendered").appendTo(this.$status);
                }
                if (data.nb_macros > 0) {
                    $('<div />').text(data.nb_macros + " macros were rendered").appendTo(this.$status);
                }
                if (typeof data.callgraph !== 'undefined') {
                    this.$callgraph.html(data.callgraph);
                }
            });
        }

    });

})(PhpDebugBar.$);
