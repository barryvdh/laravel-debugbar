(function($) {

    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for the displaying cache events
     *
     * Options:
     *  - data
     */
    var LaravelCacheWidget = PhpDebugBar.Widgets.LaravelCacheWidget = PhpDebugBar.Widget.extend({

        tagName: 'ul',

        className: csscls('timeline cache'),

        onForgetClick: function(e, el) {
            e.stopPropagation();

            $.ajax({
                url: $(el).attr("data-url"),
                type: 'DELETE',
                success: function(result) {
                    $(el).fadeOut(200);
                }
            });
        },

        render: function() {
            this.bindAttr('data', function(data) {
                this.$el.empty();
                if (data.measures) {
                    var self = this;

                    for (var i = 0; i < data.measures.length; i++) {
                        var measure = data.measures[i];
                        var m = $('<div />').addClass(csscls('measure')),
                            li = $('<li />'),
                            left = (measure.relative_start * 100 / data.duration).toFixed(2),
                            width = Math.min((measure.duration * 100 / data.duration).toFixed(2), 100 - left);

                        m.append($('<span />').addClass(csscls('value')).css({
                            left: left + "%",
                            width: width + "%"
                        }));
                        m.append($('<span />').addClass(csscls('label')).text(measure.label + " (" + measure.duration_str + ")"));

                        if (measure.collector) {
                            $('<span />').addClass(csscls('collector')).text(measure.collector).appendTo(m);
                        }

                        m.appendTo(li);
                        this.$el.append(li);

                        if (measure.params && !$.isEmptyObject(measure.params)) {

                            if (measure.params.delete && measure.params.key) {
                                $('<a />')
                                    .addClass(csscls('forget'))
                                    .text('forget')
                                    .attr('data-url', measure.params.delete)
                                    .one('click', function(e) { self.onForgetClick(e, this); })
                                    .appendTo(m);
                            }

                            var table = $('<table><tr><th colspan="2">Params</th></tr></table>').addClass(csscls('params')).appendTo(li);
                            for (var key in measure.params) {
                                if (typeof measure.params[key] !== 'function') {
                                    table.append('<tr><td class="' + csscls('name') + '">' + key + '</td><td class="' + csscls('value') +
                                    '"><pre><code>' + measure.params[key] + '</code></pre></td></tr>');
                                }
                            }
                            li.css('cursor', 'pointer').click(function() {
                                var table = $(this).find('table');
                                if (table.is(':visible')) {
                                    table.hide();
                                } else {
                                    table.show();
                                }
                            });
                        }
                    }
                }
            });
        }



    });

})(PhpDebugBar.$);
