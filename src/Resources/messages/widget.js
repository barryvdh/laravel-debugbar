(function ($) {

    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for the MessagesCollector
     *
     * Extends the original MessagesCollector class under the hood
     */
    var MessagesWidget = PhpDebugBar.Widgets.MessagesWidget = PhpDebugBar.Widgets.MessagesWidget.extend({

        render: function() {
            var self = this;

            this.$list = new PhpDebugBar.Widgets.ListWidget({ itemRenderer: function(li, value) {
                    if (value.message_html) {
                        var val = $('<span />').addClass(csscls('value')).html(value.message_html).appendTo(li);
                    } else {
                        var m = value.message;
                        if (m.length > 100) {
                            m = m.substr(0, 100) + "...";
                        }

                        var val = $('<span />').addClass(csscls('value')).text(m).appendTo(li);
                        if (!value.is_string || value.message.length > 100) {
                            var prettyVal = value.message;
                            if (!value.is_string) {
                                prettyVal = null;
                            }
                            li.css('cursor', 'pointer').click(function () {
                                if (val.hasClass(csscls('pretty'))) {
                                    val.text(m).removeClass(csscls('pretty'));
                                } else {
                                    prettyVal = prettyVal || createCodeBlock(value.message, 'php');
                                    val.addClass(csscls('pretty')).empty().append(prettyVal);
                                }
                            });
                        }
                    }

                    if (value.collector) {
                        $('<span />').addClass(csscls('collector')).text(value.collector).prependTo(li);
                    }
                    if (value.label) {
                        val.addClass(csscls(value.label));

                        var $wrapper = $('<div />').addClass(csscls('label-wrap'));

                        $('<span />').addClass(csscls('label-called-from'))
                            .attr('title', value.file_path)
                            .text(value.file_name + ':' + value.file_line)
                            .appendTo($wrapper);
                        $('<span />').addClass(csscls('label-separator')).text('// ').appendTo($wrapper);
                        $('<span />').addClass(csscls('label')).text(value.label).appendTo($wrapper);

                        $wrapper.prependTo(li);
                    }
                }});

            this.$list.$el.appendTo(this.$el);
            this.$toolbar = $('<div><i class="phpdebugbar-fa phpdebugbar-fa-search"></i></div>').addClass(csscls('toolbar')).appendTo(this.$el);

            $('<input type="text" />')
                .on('change', function() { self.set('search', this.value); })
                .appendTo(this.$toolbar);

            this.bindAttr('data', function(data) {
                this.set({ exclude: [], search: '' });
                this.$toolbar.find(csscls('.filter')).remove();

                var filters = [], self = this;
                for (var i = 0; i < data.length; i++) {
                    if (!data[i].label || $.inArray(data[i].label, filters) > -1) {
                        continue;
                    }
                    filters.push(data[i].label);
                    $('<a />')
                        .addClass(csscls('filter'))
                        .text(data[i].label)
                        .attr('rel', data[i].label)
                        .on('click', function() { self.onFilterClick(this); })
                        .appendTo(this.$toolbar);
                }

                for (var i = 0; i < data.length; i++) {
                    if (!data[i].file_name || $.inArray(data[i].file_name, filters) > -1) {
                        continue;
                    }
                    filters.push(data[i].file_name);
                    $('<a />')
                        .addClass(csscls('filter') + ' ' + csscls('filter-file'))
                        .text(data[i].file_name)
                        .attr('rel', data[i].label)
                        .attr('data-file_name', data[i].file_name)
                        .on('click', function() { self.onFilterClick(this); })
                        .appendTo(this.$toolbar);
                }
            });

            this.bindAttr(['exclude', 'excludeFiles', 'search'], function() {
                var data = this.get('data'),
                    exclude = this.get('exclude'),
                    excludeFiles = this.get('excludeFiles'),
                    search = this.get('search'),
                    caseless = false,
                    fdata = [];

                if (search && search === search.toLowerCase()) {
                    caseless = true;
                }

                for (var i = 0; i < data.length; i++) {
                    var message = caseless ? data[i].message.toLowerCase() : data[i].message;

                    if ((!search || message.indexOf(search) > -1)) {

                        if ((!data[i].label || $.inArray(data[i].label, exclude) === -1)
                                && (!data[i].file_name || $.inArray(data[i].file_name, excludeFiles) === -1)
                        ) {
                            fdata.push(data[i]);
                        }

                    }
                }

                this.$list.set('data', fdata);
            });
        },

        onFilterClick: function(el) {
            $(el).toggleClass(csscls('excluded'));

            var excludedLabels = [];
            var excludedFiles = [];
            this.$toolbar.find(csscls('.filter') + csscls('.excluded')).each(function() {
                if (this.dataset.file_name) {
                    excludedFiles.push(this.dataset.file_name);
                    return;
                }

                excludedLabels.push(this.rel);
            });

            this.set('exclude', $.unique(excludedLabels));
            this.set('excludeFiles', $.unique(excludedFiles));
        }
    });

})(PhpDebugBar.$);
