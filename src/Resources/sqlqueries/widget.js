(function ($) {

    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for the displaying sql queries
     *
     * Options:
     *  - data
     */
    var LaravelSQLQueriesWidget = PhpDebugBar.Widgets.LaravelSQLQueriesWidget = PhpDebugBar.Widget.extend({

        className: csscls('sqlqueries'),

        onFilterClick: function (el) {
            $(el).toggleClass(csscls('excluded'));

            var excludedLabels = [];
            this.$toolbar.find(csscls('.filter') + csscls('.excluded')).each(function () {
                excludedLabels.push(this.rel);
            });

            this.$list.$el.find("li[connection=" + $(el).attr("rel") + "]").toggle();

            this.set('exclude', excludedLabels);
        },

        onCopyToClipboard: function (el) {
            var code = $(el).parent('li').find('code').get(0);
            var copy = function () {
                try {
                    document.execCommand('copy');
                    alert('Query copied to the clipboard');
                } catch (err) {
                    console.log('Oops, unable to copy');
                }
            };
            var select = function (node) {
                if (document.selection) {
                    var range = document.body.createTextRange();
                    range.moveToElementText(node);
                    range.select();
                } else if (window.getSelection) {
                    var range = document.createRange();
                    range.selectNodeContents(node);
                    window.getSelection().removeAllRanges();
                    window.getSelection().addRange(range);
                }
                copy();
                window.getSelection().removeAllRanges();
            };
            select(code);
        },

        render: function () {
            this.$status = $('<div />').addClass(csscls('status')).appendTo(this.$el);

            this.$toolbar = $('<div></div>').addClass(csscls('toolbar')).appendTo(this.$el);

            var filters = [], self = this;

            this.$list = new PhpDebugBar.Widgets.ListWidget({ itemRenderer: function (li, stmt) {
                if (stmt.type === 'transaction') {
                    $('<strong />').addClass(csscls('sql')).addClass(csscls('name')).text(stmt.sql).appendTo(li);
                } else {
                    $('<code />').addClass(csscls('sql')).html(PhpDebugBar.Widgets.highlight(stmt.sql, 'sql')).appendTo(li);
                }
                if (stmt.width_percent) {
                    $('<div></div>').addClass(csscls('bg-measure')).append(
                        $('<div></div>').addClass(csscls('value')).css({
                            left: stmt.start_percent + '%',
                            width: Math.max(stmt.width_percent, 0.01) + '%',
                        })
                    ).appendTo(li);
                }
                if (stmt.duration_str) {
                    $('<span title="Duration" />').addClass(csscls('duration')).text(stmt.duration_str).appendTo(li);
                }
                if (stmt.memory_str) {
                    $('<span title="Memory usage" />').addClass(csscls('memory')).text(stmt.memory_str).appendTo(li);
                }
                if (typeof(stmt.row_count) != 'undefined') {
                    $('<span title="Row count" />').addClass(csscls('row-count')).text(stmt.row_count).appendTo(li);
                }
                if (typeof(stmt.stmt_id) != 'undefined' && stmt.stmt_id) {
                    $('<span title="Prepared statement ID" />').addClass(csscls('stmt-id')).text(stmt.stmt_id).appendTo(li);
                }
                if (stmt.connection) {
                    $('<span title="Connection" />').addClass(csscls('database')).text(stmt.connection).appendTo(li);
                    li.attr("connection",stmt.connection);
                    if ( $.inArray(stmt.connection, filters) == -1 ) {
                        filters.push(stmt.connection);
                        $('<a />')
                            .addClass(csscls('filter'))
                            .text(stmt.connection)
                            .attr('rel', stmt.connection)
                            .on('click', function () {
                                self.onFilterClick(this); })
                            .appendTo(self.$toolbar);
                        if (filters.length > 1) {
                            self.$toolbar.show();
                            self.$list.$el.css("margin-bottom","20px");
                        }
                    }
                }
                if (typeof(stmt.is_success) != 'undefined' && !stmt.is_success) {
                    li.addClass(csscls('error'));
                    li.append($('<span />').addClass(csscls('error')).text("[" + stmt.error_code + "] " + stmt.error_message));
                }
                if (stmt.show_copy) {
                    $('<span title="Copy to clipboard" />')
                        .addClass(csscls('copy-clipboard'))
                        .css('cursor', 'pointer')
                        .on('click', function (event) {
                            self.onCopyToClipboard(this);
                            event.stopPropagation();
                        })
                        .appendTo(li);
                }

                var table = $('<table><tr><th colspan="2">Metadata</th></tr></table>').addClass(csscls('params')).appendTo(li);

                if (stmt.bindings && stmt.bindings.length) {
                    table.append(function () {
                        var icon = 'thumb-tack';
                        var $icon = '<i class="phpdebugbar-fa phpdebugbar-fa-' + icon + ' phpdebugbar-text-muted"></i>';
                        var $name = $('<td />').addClass(csscls('name')).html('Bindings ' + $icon);
                        var $value = $('<td />').addClass(csscls('value'));
                        var $span = $('<span />').addClass('phpdebugbar-text-muted');

                        var index = 0;
                        var $bindings = new PhpDebugBar.Widgets.ListWidget({ itemRenderer: function (li, binding) {
                            var $index = $span.clone().text(index++ + '.');
                            li.append($index, '&nbsp;', binding).removeClass(csscls('list-item')).addClass(csscls('table-list-item'));
                        }});

                        $bindings.set('data', stmt.bindings);

                        $bindings.$el
                            .removeClass(csscls('list'))
                            .addClass(csscls('table-list'))
                            .appendTo($value);

                        return $('<tr />').append($name, $value);
                    });
                }

                if (stmt.hints && stmt.hints.length) {
                    table.append(function () {
                        var icon = 'question-circle';
                        var $icon = '<i class="phpdebugbar-fa phpdebugbar-fa-' + icon + ' phpdebugbar-text-muted"></i>';
                        var $name = $('<td />').addClass(csscls('name')).html('Hints ' + $icon);
                        var $value = $('<td />').addClass(csscls('value'));

                        var $hints = new PhpDebugBar.Widgets.ListWidget({ itemRenderer: function (li, hint) {
                            li.append(hint).removeClass(csscls('list-item')).addClass(csscls('table-list-item'));
                        }});

                        $hints.set('data', stmt.hints);
                        $hints.$el
                            .removeClass(csscls('list'))
                            .addClass(csscls('table-list'))
                            .appendTo($value);

                        return $('<tr />').append($name, $value);
                    });
                }

                if (stmt.backtrace && stmt.backtrace.length) {
                    table.append(function () {
                        var icon = 'list-ul';
                        var $icon = '<i class="phpdebugbar-fa phpdebugbar-fa-' + icon + ' phpdebugbar-text-muted"></i>';
                        var $name = $('<td />').addClass(csscls('name')).html('Backtrace ' + $icon);
                        var $value = $('<td />').addClass(csscls('value'));
                        var $span = $('<span />').addClass('phpdebugbar-text-muted');

                        var $backtrace = new PhpDebugBar.Widgets.ListWidget({ itemRenderer: function (li, source) {
                            var $parts = [
                                $span.clone().text(source.index + '.'),
                                '&nbsp;',
                            ];

                            if (source.namespace) {
                                $parts.push(source.namespace + '::');
                            }

                            $parts.push(source.name);
                            $parts.push($span.clone().text(':' + source.line));

                            li.append($parts).removeClass(csscls('list-item')).addClass(csscls('table-list-item'));
                        }});

                        $backtrace.set('data', stmt.backtrace);

                        $backtrace.$el
                            .removeClass(csscls('list'))
                            .addClass(csscls('table-list'))
                            .appendTo($value);

                        return $('<tr />').append($name, $value);
                    });
                }

                if (stmt.params && !$.isEmptyObject(stmt.params)) {
                    for (var key in stmt.params) {
                        if (typeof stmt.params[key] !== 'function') {
                            table.append('<tr><td class="' + csscls('name') + '">' + key + '</td><td class="' + csscls('value') +
                                '">' + stmt.params[key] + '</td></tr>');
                        }
                    }
                }

                li.css('cursor', 'pointer').click(function () {
                    if (table.is(':visible')) {
                        table.hide();
                    } else {
                        table.show();
                    }
                });
            }});
            this.$list.$el.appendTo(this.$el);

            this.bindAttr('data', function (data) {
                this.$list.set('data', data.statements);
                this.$status.empty();
                var stmt;

                // Search for duplicate statements.
                for (var sql = {}, duplicate = 0, i = 0; i < data.statements.length; i++) {
                    if (data.statements[i].type === 'query') {
                        stmt = data.statements[i].sql;
                        if (data.statements[i].bindings && data.statements[i].bindings.length) {
                            stmt += JSON.stringify(data.statements[i].bindings);
                        }
                        if (data.statements[i].connection) {
                            stmt += '@' + data.statements[i].connection;
                        }
                        sql[stmt] = sql[stmt] || { keys: [] };
                        sql[stmt].keys.push(i);
                    }
                }
                // Add classes to all duplicate SQL statements.
                for (stmt in sql) {
                    if (sql[stmt].keys.length > 1) {
                        duplicate += sql[stmt].keys.length;

                        for (i = 0; i < sql[stmt].keys.length; i++) {
                            this.$list.$el.find('.' + csscls('list-item')).eq(sql[stmt].keys[i])
                                .addClass(csscls('sql-duplicate'))
                                .addClass(csscls('sql-duplicate-' + duplicate));
                        }
                    }
                }

                var t = $('<span />').text(data.nb_statements + " statements were executed").appendTo(this.$status);
                if (data.nb_failed_statements) {
                    t.append(", " + data.nb_failed_statements + " of which failed");
                }
                if (duplicate) {
                    t.append(", " + duplicate + " of which were duplicated");
                    t.append(", " + (data.nb_statements - duplicate) + " unique");

                    // add toggler for displaying only duplicated queries
                    var duplicatedText = "Show only duplicated";
                    var allText = "Show All";
                    var id = "phpdebugbar-show-duplicates";
                    t.append(". <a id='" + id + "'>" + duplicatedText + "</a>");

                    $(".phpdebugbar #" + id).click(function () {
                        var $this = $(this);
                        $this.toggleClass("shown_duplicated");
                        $this.text($this.hasClass("shown_duplicated") ? allText : duplicatedText);
                        $(".phpdebugbar-widgets-sqlqueries .phpdebugbar-widgets-list-item")
                            .not(".phpdebugbar-widgets-sql-duplicate")
                            .toggle();

                        return false;
                    });

                }
                if (data.accumulated_duration_str) {
                    this.$status.append($('<span title="Accumulated duration" />').addClass(csscls('duration')).text(data.accumulated_duration_str));
                }
                if (data.memory_usage_str) {
                    this.$status.append($('<span title="Memory usage" />').addClass(csscls('memory')).text(data.memory_usage_str));
                }
            });
        }

    });

})(PhpDebugBar.$);
