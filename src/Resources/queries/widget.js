(function($) {

    let css = PhpDebugBar.utils.makecsscls('phpdebugbar-');
    let csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for displaying sql queries.
     *
     * Options:
     *  - data
     */
    const QueriesWidget = PhpDebugBar.Widgets.LaravelQueriesWidget = PhpDebugBar.Widget.extend({

        className: csscls('sqlqueries'),

        duplicateQueries: new Set(),

        hiddenConnections: new Set(),

        copyToClipboard: function (code) {
            if (document.selection) {
                const range = document.body.createTextRange();
                range.moveToElementText(code);
                range.select();
            } else if (window.getSelection) {
                const range = document.createRange();
                range.selectNodeContents(code);
                window.getSelection().removeAllRanges();
                window.getSelection().addRange(range);
            }

            var isCopied = false;
            try {
                isCopied = document.execCommand('copy');
                console.log('Query copied to the clipboard');
            } catch (err) {
                alert('Oops, unable to copy');
            }

            window.getSelection().removeAllRanges();

            return isCopied;
        },

        explainMysql: function ($element, statement, rows, visual) {
            const headings = [];
            for (const key in rows[0]) {
                headings.push($('<th/>').text(key));
            }

            const values = [];
            for (const row of rows) {
                const $tr = $('<tr/>');
                for (const key in row) {
                    $tr.append($('<td/>').text(row[key]));
                }
                values.push($tr);
            }

            const $table = $('<table><thead></thead><tbody></tbody></table>').addClass(csscls('explain'));
            $table.find('thead').append($('<tr/>').append(headings));
            $table.find('tbody').append(values);

            $element.append($table);
            if (visual) {
                $element.append(this.explainVisual(statement, visual.confirm));
            }
        },

        explainPgsql: function ($element, statement, rows, visual) {
            const $ul = $('<ul />').addClass(csscls('table-list'));
            const $li = $('<li />').addClass(csscls('table-list-item'));

            for (const row of rows) {
                $ul.append($li.clone().html($('<span/>').text(row).text().replaceAll(' ', '&nbsp;')));
            }

            $element.append([$ul, this.explainVisual(statement, visual.confirm)]);
        },

        explainVisual: function (statement, confirmMessage) {
            const $explainLink = $('<a href="#" target="_blank" rel="noopener"/>')
                .addClass(csscls('visual-link'));
            const $explainButton = $('<a>Visual Explain</a>')
                .addClass(csscls('visual-explain'))
                .on('click', () => {
                    if (!confirm(statement.explain['visual-confirm'])) return;
                    fetch(statement.explain.url, {
                        method: "POST",
                        body: JSON.stringify({
                            connection: statement.explain.connection,
                            query: statement.explain.query,
                            bindings: statement.bindings,
                            hash: statement.explain.hash,
                            mode: 'visual',
                        }),
                    }).then(response => {
                        response.json()
                            .then(json => {
                                if (!response.ok) return alert(json.message);
                                $explainLink.attr('href', json.data).text(json.data);
                                window.open(json.data, '_blank', 'noopener');
                            })
                            .catch(err => alert(`Response body could not be parsed. (${err})`));
                    }).catch(e => {
                        alert(e.message);
                    });
                });

            return $('<div/>').append([$explainButton, $explainLink]);
        },

        identifyDuplicates: function(statements) {
            if (! Array.isArray(statements)) statements = [];

            const makeStatementHash = (statement) => {
                return [
                    statement.sql,
                    statement.connection,
                    JSON.stringify(statement.bindings),
                ].join('::');
            };

            const countedStatements = {};
            for (const statement of statements) {
                if (statement.type === 'query') {
                    countedStatements[makeStatementHash(statement)] = (countedStatements[makeStatementHash(statement)] ?? 0) + 1;
                }
            }

            this.duplicateQueries = new Set();
            for (const statement of statements) {
                if (countedStatements[makeStatementHash(statement)] > 1) {
                    this.duplicateQueries.add(statement);
                }
            }
        },

        render: function () {
            const $status = $('<div />').addClass(csscls('status')).appendTo(this.$el);

            const $list = new PhpDebugBar.Widgets.ListWidget({
                itemRenderer: this.renderQuery.bind(this),
            });
            this.$el.append($list.$el);

            this.bindAttr('data', function (data) {
                this.identifyDuplicates(data.statements);

                this.renderStatus($status, data);
                $list.set('data', data.statements);
            });
        },

        renderStatus: function ($status, data) {
            $status.empty();

            const connections = new Set();
            for (const statement of data.statements) {
                connections.add(statement.connection);
            }

            const $text = $('<span />').text(`${data.nb_statements} ${data.nb_statements == 1 ? 'statement was' : 'statements were'} executed`);
            if (data.nb_excluded_statements) {
                $text.append(`, ${data.nb_excluded_statements} ${data.nb_excluded_statements == 1 ? 'has' : 'have'} been excluded`);
            }
            if (data.nb_failed_statements > 0 || this.duplicateQueries.size > 0) {
                const details = [];
                if (data.nb_failed_statements) {
                    details.push(`${data.nb_failed_statements} failed`);
                }
                if (this.duplicateQueries.size > 0) {
                    details.push(`${this.duplicateQueries.size} ${this.duplicateQueries.size == 1 ? 'duplicate' : 'duplicates'}`);
                }
                $text.append(` (${details.join(', ')})`);
            }
            $status.append($text);

            const filters = [];
            if (this.duplicateQueries.size > 0) {
                filters.push($('<a />')
                    .text('Show only duplicates')
                    .addClass(csscls('duplicates'))
                    .click((event) => {
                        if ($(event.target).text() === 'Show only duplicates') {
                            $(event.target).text('Show All');
                            this.$el.find('[data-duplicate=false]').hide();
                        } else {
                            $(event.target).text('Show only duplicates');
                            this.$el.find('[data-duplicate]').show();
                        }
                    })
                );
            }
            if (connections.size > 1) {
                for (const connection of connections.values()) {
                    filters.push($('<a />')
                        .addClass(csscls('connection'))
                        .text(connection)
                        .attr({'data-filter': connection, 'data-active': true})
                        .on('click', (event) => {
                            if ($(event.target).attr('data-active') === 'true') {
                                $(event.target).attr('data-active', false).css('opacity', 0.3);
                                this.hiddenConnections.add($(event.target).attr('data-filter'));
                            } else {
                                $(event.target).attr('data-active', true).css('opacity', 1.0);
                                this.hiddenConnections.delete($(event.target).attr('data-filter'));
                            }

                            this.$el.find(`[data-connection]`).show();
                            for (const hiddenConnection of this.hiddenConnections) {
                                this.$el.find(`[data-connection="${hiddenConnection}"]`).hide();
                            }
                        })
                    );
                }
            }
            $status.append(filters);

            if (data.accumulated_duration_str) {
                $status.append($('<span title="Accumulated duration" />').addClass(csscls('duration')).text(data.accumulated_duration_str));
            }
            if (data.memory_usage_str) {
                $status.append($('<span title="Memory usage" />').addClass(csscls('memory')).text(data.memory_usage_str));
            }
        },

        renderQuery: function ($li, statement) {
            if (statement.type === 'transaction') {
                $li.attr('data-connection', statement.connection)
                    .attr('data-duplicate', false)
                    .append($('<strong />').addClass(csscls('sql name')).text(statement.sql));
            } else {
                const $code = $('<code />').html(PhpDebugBar.Widgets.highlight(statement.sql, 'sql')).addClass(csscls('sql')),
                    duplicated = this.duplicateQueries.has(statement);
                $li.attr('data-connection', statement.connection)
                    .attr('data-duplicate', duplicated)
                    .toggleClass(csscls('sql-duplicate'), duplicated)
                    .append($code);

                if (statement.show_copy) {
                    $('<span title="Copy to clipboard" />')
                        .addClass(csscls('copy-clipboard'))
                        .css('cursor', 'pointer')
                        .on('click', (event) => {
                            event.stopPropagation();
                            if (this.copyToClipboard($code.get(0))) {
                                $(event.target).addClass(csscls('copy-clipboard-check'));
                                setTimeout(function(){
                                    $(event.target).removeClass(csscls('copy-clipboard-check'));
                                }, 2000)
                            }
                        }).prependTo($li);
                }
            }

            if (statement.width_percent) {
                $('<div />').addClass(csscls('bg-measure')).append(
                    $('<div />').addClass(csscls('value')).css({
                        left: `${statement.start_percent}%`,
                        width: `${Math.max(statement.width_percent, 0.01)}%`,
                    })
                ).appendTo($li);
            }

            if ('is_success' in statement && !statement.is_success) {
                $li.addClass(csscls('error')).prepend($('<span />').addClass(csscls('error')).text(`[${statement.error_code}] ${statement.error_message}`));
            }
            if (statement.duration_str) {
                $li.prepend($('<span title="Duration" />').addClass(csscls('duration')).text(statement.duration_str));
            }
            if (statement.memory_str) {
                $li.prepend($('<span title="Memory usage" />').addClass(csscls('memory')).text(statement.memory_str));
            }
            if (statement.connection) {
                $li.prepend($('<span title="Connection" />').addClass(csscls('database')).text(statement.connection));
            }
            if (statement.xdebug_link) {
                $('<span title="Filename" />')
                    .addClass(csscls('filename'))
                    .text(statement.xdebug_link.filename + '#' + (statement.xdebug_link.line || '?'))
                    .append($('<a/>')
                        .attr('href', statement.xdebug_link.url)
                        .addClass(csscls('editor-link'))
                        .on('click', event => {
                            event.stopPropagation();
                            if (statement.xdebug_link.ajax) {
                                event.preventDefault();
                                fetch(statement.xdebug_link.url);
                            }
                        })
                    ).prependTo($li);
            }

            const $details = $('<table></table>').addClass(csscls('params'))
            if (statement.bindings && !$.isEmptyObject(statement.bindings)) {
                $details.append(this.renderDetailStrings('Bindings', 'thumb-tack', statement.bindings, true));
            }
            if (statement.hints && !$.isEmptyObject(statement.hints)) {
                $details.append(this.renderDetailStrings('Hints', 'question-circle', statement.hints));
            }
            if (statement.backtrace && !$.isEmptyObject(statement.backtrace)) {
                $details.append(this.renderDetailBacktrace('Backtrace', 'list-ul', statement.backtrace));
            }
            if (statement.explain && ['mariadb', 'mysql'].includes(statement.explain.driver)) {
                $details.append(this.renderDetailExplain('Performance', 'tachometer', statement, this.explainMysql.bind(this)));
            }
            if (statement.explain && statement.explain.driver === 'pgsql') {
                $details.append(this.renderDetailExplain('Performance', 'tachometer', statement, this.explainPgsql.bind(this)));
            }

            if($details.children().length) {
                $li.addClass(csscls('expandable'))
                    .on('click', (event) => {
                        if (window.getSelection().type == "Range") {
                            return;
                        }

                        if ($(event.target).closest(`.${csscls('params')}`).length) {
                            return;
                        }

                        if ($li.find(`.${csscls('params')}:visible`).length) {
                            $li.find(`.${csscls('params')}`).css('display', 'none');
                        } else {
                            $li.find(`.${csscls('params')}`).css('display', 'table');
                        }
                    });
            }

            $li.append($details);
        },

        renderDetail: function (caption, icon, $value) {
           return $('<tr />').append(
                $('<td />').addClass(csscls('name')).html(caption + ((icon || '') && `<i class="${css('text-muted fa fa-'+icon)}" />`)),
                $('<td />').addClass(csscls('value')).append($value),
            );
        },

        renderDetailStrings: function (caption, icon, values, showLineNumbers = false) {
            const $ul = $('<ul />').addClass(csscls('table-list'));
            const $li = $('<li />').addClass(csscls('table-list-item'));
            const $muted = $('<span />').addClass(css('text-muted'));

            $.each(values, (i, value) => {
                if (showLineNumbers) {
                    $ul.append($li.clone().append([$muted.clone().text(`${i}:`), '&nbsp;', $('<span/>').text(value)]));
                } else {
                    if (caption === 'Hints') {
                        $ul.append($li.clone().append(value));
                    } else {
                        $ul.append($li.clone().text(value));
                    }
                }
            });

            return this.renderDetail(caption, icon, $ul);
        },

        renderDetailBacktrace: function (caption, icon, traces) {
            const $muted = $('<span />').addClass(css('text-muted'));

            const values = [];
            for (const trace of traces.values()) {
                const $span = $('<span/>').text(trace.name || trace.file);
                if (trace.namespace) {
                    $span.prepend(`${trace.namespace}::`);
                }
                if (trace.line) {
                    $span.append($muted.clone().text(`:${trace.line}`));
                }

                values.push($span.text());
            }

            return this.renderDetailStrings(caption, icon, values);
        },

        renderDetailExplain: function (caption, icon, statement, explainFn) {
            const $btn = $('<button/>')
                .text('Run EXPLAIN')
                .addClass(csscls('explain-btn'))
                .on('click', () => {
                    fetch(statement.explain.url, {
                        method: "POST",
                        body: JSON.stringify({
                            connection: statement.explain.connection,
                            query: statement.explain.query,
                            bindings: statement.bindings,
                            hash: statement.explain.hash,
                        }),
                    }).then(response => {
                        response.json()
                            .then(json => {
                                if (!response.ok) return alert(json.message);
                                $detail.find(`.${csscls('value')}`).children().remove();
                                explainFn($detail.find(`.${csscls('value')}`), statement, json.data, json.visual);
                            })
                            .catch(err => alert(`Response body could not be parsed. (${err})`));
                    }).catch(e => {
                        alert(e.message);
                    });
                });
            const $detail = this.renderDetail(caption, icon, $btn);

            return $detail;
        },
    });
})(PhpDebugBar.$);
