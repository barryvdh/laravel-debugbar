(function($) {

    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for the displaying cache events
     *
     * Options:
     *  - data
     */
    var LaravelCacheWidget = PhpDebugBar.Widgets.LaravelCacheWidget = PhpDebugBar.Widgets.TimelineWidget.extend({

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
            LaravelCacheWidget.__super__.render.apply(this);

            this.bindAttr('data', function(data) {

                if (data.measures) {
                    var self = this;
                    var lines = this.$el.find('.'+csscls('measure'));

                    for (var i = 0; i < data.measures.length; i++) {
                        var measure = data.measures[i];
                        var m = lines[i];

                        if (measure.params && !$.isEmptyObject(measure.params)) {

                            if (measure.params.delete && measure.params.key) {
                                $('<a />')
                                    .addClass(csscls('forget'))
                                    .text('forget')
                                    .attr('data-url', measure.params.delete)
                                    .one('click', function(e) { self.onForgetClick(e, this); })
                                    .appendTo(m);
                            }
                        }
                    }
                }
            });
        }
    });

})(PhpDebugBar.$);
