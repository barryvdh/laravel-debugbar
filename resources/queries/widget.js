(function () {
    const css = PhpDebugBar.utils.makecsscls('phpdebugbar-');
    const csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for displaying sql queries.
     *
     * Options:
     *  - data
     */
    class LaravelQueriesWidget extends PhpDebugBar.Widget {
        get className() {
            return csscls('sqlqueries');
        }

        constructor() {
            super();
            this.duplicateQueries = new Set();
            this.hiddenConnections = new Set();
        }

        copyToClipboard(code) {
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

            let isCopied = false;
            try {
                isCopied = document.execCommand('copy');
                console.log('Query copied to the clipboard');
            } catch (err) {
                alert('Oops, unable to copy'); // eslint-disable-line no-alert
            }

            window.getSelection().removeAllRanges();

            return isCopied;
        }

        explainMysql(element, statement, rows, visual) {
            const headings = [];
            for (const key in rows[0]) {
                const th = document.createElement('th');
                th.textContent = key;
                headings.push(th);
            }

            const values = [];
            for (const row of rows) {
                const tr = document.createElement('tr');
                for (const key in row) {
                    const td = document.createElement('td');
                    td.textContent = row[key];
                    tr.append(td);
                }
                values.push(tr);
            }

            const table = document.createElement('table');
            table.classList.add(csscls('explain'));
            const thead = document.createElement('thead');
            const tbody = document.createElement('tbody');
            const headerRow = document.createElement('tr');
            headerRow.append(...headings);
            thead.append(headerRow);
            tbody.append(...values);
            table.append(thead, tbody);

            element.append(table);
            if (visual) {
                element.append(this.explainVisual(statement, visual.confirm));
            }
        }

        explainPgsql(element, statement, rows, visual) {
            const ul = document.createElement('ul');
            ul.classList.add(csscls('table-list'));

            for (const row of rows) {
                const li = document.createElement('li');
                li.classList.add(csscls('table-list-item'));
                const span = document.createElement('span');
                span.textContent = row;
                li.innerHTML = span.textContent.replaceAll(' ', '&nbsp;');
                ul.append(li);
            }

            element.append(ul, this.explainVisual(statement, visual.confirm));
        }

        explainVisual(statement, confirmMessage) {
            const explainLink = document.createElement('a');
            explainLink.href = '#';
            explainLink.target = '_blank';
            explainLink.rel = 'noopener';
            explainLink.classList.add(csscls('visual-link'));

            const explainButton = document.createElement('a');
            explainButton.textContent = 'Visual Explain';
            explainButton.classList.add(csscls('visual-explain'));
            explainButton.addEventListener('click', () => {
                if (!confirm(confirmMessage)) // eslint-disable-line no-alert
                    return;
                fetch(statement.explain.url, {
                    method: 'POST',
                    body: JSON.stringify({
                        connection: statement.explain.connection,
                        query: statement.explain.query,
                        bindings: statement.bindings,
                        hash: statement.explain.hash,
                        mode: 'visual'
                    })
                }).then((response) => {
                    response.json()
                        .then((json) => {
                            if (!response.ok)
                                return alert(json.message); // eslint-disable-line no-alert
                            explainLink.href = json.data;
                            explainLink.textContent = json.data;
                            window.open(json.data, '_blank', 'noopener');
                        })
                        .catch(err => alert(`Response body could not be parsed. (${err})`)); // eslint-disable-line no-alert
                }).catch((e) => {
                    alert(e.message); // eslint-disable-line no-alert
                });
            });

            const div = document.createElement('div');
            div.append(explainButton, explainLink);
            return div;
        }

        identifyDuplicates(statements) {
            if (!Array.isArray(statements))
                statements = [];

            const makeStatementHash = (statement) => {
                return [
                    statement.sql,
                    statement.connection,
                    JSON.stringify(statement.bindings)
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
        }

        render() {
            const status = document.createElement('div');
            status.classList.add(csscls('status'));
            this.el.append(status);

            const list = new PhpDebugBar.Widgets.ListWidget({
                itemRenderer: this.renderQuery.bind(this)
            });
            this.el.append(list.el);

            this.bindAttr('data', function (data) {
                this.identifyDuplicates(data.statements);

                this.renderStatus(status, data);
                list.set('data', data.statements);
            });
        }

        renderStatus(status, data) {
            status.innerHTML = '';

            const connections = new Set();
            for (const statement of data.statements) {
                connections.add(statement.connection);
            }

            const text = document.createElement('span');
            let textContent = `${data.nb_statements} ${data.nb_statements === 1 ? 'statement was' : 'statements were'} executed`;
            if (data.nb_excluded_statements) {
                textContent += `, ${data.nb_excluded_statements} ${data.nb_excluded_statements === 1 ? 'has' : 'have'} been excluded`;
            }
            if (data.nb_failed_statements > 0 || this.duplicateQueries.size > 0) {
                const details = [];
                if (data.nb_failed_statements) {
                    details.push(`${data.nb_failed_statements} failed`);
                }
                if (this.duplicateQueries.size > 0) {
                    details.push(`${this.duplicateQueries.size} ${this.duplicateQueries.size === 1 ? 'duplicate' : 'duplicates'}`);
                }
                textContent += ` (${details.join(', ')})`;
            }
            text.textContent = textContent;
            status.append(text);

            const filters = [];
            if (this.duplicateQueries.size > 0) {
                const duplicatesLink = document.createElement('a');
                duplicatesLink.textContent = 'Show only duplicates';
                duplicatesLink.classList.add(csscls('duplicates'));
                duplicatesLink.addEventListener('click', (event) => {
                    if (event.target.textContent === 'Show only duplicates') {
                        event.target.textContent = 'Show All';
                        this.el.querySelectorAll('[data-duplicate=false]').forEach(el => el.style.display = 'none');
                    } else {
                        event.target.textContent = 'Show only duplicates';
                        this.el.querySelectorAll('[data-duplicate]').forEach(el => el.style.display = '');
                    }
                });
                filters.push(duplicatesLink);
            }
            if (connections.size > 1) {
                for (const connection of connections.values()) {
                    const connectionLink = document.createElement('a');
                    connectionLink.classList.add(csscls('connection'));
                    connectionLink.textContent = connection;
                    connectionLink.setAttribute('data-filter', connection);
                    connectionLink.setAttribute('data-active', 'true');
                    connectionLink.addEventListener('click', (event) => {
                        if (event.target.getAttribute('data-active') === 'true') {
                            event.target.setAttribute('data-active', 'false');
                            event.target.style.opacity = '0.3';
                            this.hiddenConnections.add(event.target.getAttribute('data-filter'));
                        } else {
                            event.target.setAttribute('data-active', 'true');
                            event.target.style.opacity = '1.0';
                            this.hiddenConnections.delete(event.target.getAttribute('data-filter'));
                        }

                        this.el.querySelectorAll('[data-connection]').forEach(el => el.style.display = '');
                        for (const hiddenConnection of this.hiddenConnections) {
                            this.el.querySelectorAll(`[data-connection="${hiddenConnection}"]`).forEach(el => el.style.display = 'none');
                        }
                    });
                    filters.push(connectionLink);
                }
            }
            status.append(...filters);

            if (data.accumulated_duration_str) {
                const duration = document.createElement('span');
                duration.title = 'Accumulated duration';
                duration.classList.add(csscls('duration'));
                duration.textContent = data.accumulated_duration_str;
                status.append(duration);
            }
            if (data.memory_usage_str) {
                const memory = document.createElement('span');
                memory.title = 'Memory usage';
                memory.classList.add(csscls('memory'));
                memory.textContent = data.memory_usage_str;
                status.append(memory);
            }
        }

        renderQuery(li, statement) {
            if (statement.type === 'transaction') {
                li.setAttribute('data-connection', statement.connection);
                li.setAttribute('data-duplicate', 'false');
                const strong = document.createElement('strong');
                strong.classList.add(csscls('sql'), csscls('name'));
                strong.textContent = statement.sql;
                li.append(strong);
            } else {
                if (statement.slow) {
                    li.classList.add(csscls('sql-slow'));
                }
                const code = document.createElement('code');
                code.innerHTML = PhpDebugBar.Widgets.highlight(statement.sql, 'sql');
                code.classList.add(csscls('sql'));
                const duplicated = this.duplicateQueries.has(statement);
                li.setAttribute('data-connection', statement.connection);
                li.setAttribute('data-duplicate', duplicated);
                if (duplicated) {
                    li.classList.add(csscls('sql-duplicate'));
                }
                li.append(code);

                if (statement.show_copy) {
                    const copyIcon = document.createElement('span');
                    copyIcon.title = 'Copy to clipboard';
                    copyIcon.classList.add(csscls('copy-clipboard'));
                    copyIcon.style.cursor = 'pointer';
                    copyIcon.addEventListener('click', (event) => {
                        event.stopPropagation();
                        if (this.copyToClipboard(code)) {
                            event.target.classList.add(csscls('copy-clipboard-check'));
                            setTimeout(() => {
                                event.target.classList.remove(csscls('copy-clipboard-check'));
                            }, 2000);
                        }
                    });
                    li.prepend(copyIcon);
                }
            }

            if (statement.width_percent) {
                const bgMeasure = document.createElement('div');
                bgMeasure.classList.add(csscls('bg-measure'));
                const value = document.createElement('div');
                value.classList.add(csscls('value'));
                value.style.left = `${statement.start_percent}%`;
                value.style.width = `${Math.max(statement.width_percent, 0.01)}%`;
                bgMeasure.append(value);
                li.append(bgMeasure);
            }

            if ('is_success' in statement && !statement.is_success) {
                li.classList.add(csscls('error'));
                const errorSpan = document.createElement('span');
                errorSpan.classList.add(csscls('error'));
                errorSpan.textContent = `[${statement.error_code}] ${statement.error_message}`;
                li.prepend(errorSpan);
            }
            if (statement.duration_str) {
                const duration = document.createElement('span');
                duration.title = 'Duration';
                duration.classList.add(csscls('duration'));
                duration.textContent = statement.duration_str;
                li.prepend(duration);
            }
            if (statement.memory_str) {
                const memory = document.createElement('span');
                memory.title = 'Memory usage';
                memory.classList.add(csscls('memory'));
                memory.textContent = statement.memory_str;
                li.prepend(memory);
            }
            if (statement.connection) {
                const database = document.createElement('span');
                database.title = 'Connection';
                database.classList.add(csscls('database'));
                database.textContent = statement.connection;
                li.prepend(database);
            }
            if (statement.xdebug_link) {
                const filename = document.createElement('span');
                filename.title = 'Filename';
                filename.classList.add(csscls('filename'));
                filename.textContent = `${statement.xdebug_link.filename}#${statement.xdebug_link.line || '?'}`;
                const link = document.createElement('a');
                link.href = statement.xdebug_link.url;
                link.classList.add(csscls('editor-link'));
                link.addEventListener('click', (event) => {
                    event.stopPropagation();
                    if (statement.xdebug_link.ajax) {
                        event.preventDefault();
                        fetch(statement.xdebug_link.url);
                    }
                });
                filename.append(link);
                li.prepend(filename);
            }

            const details = document.createElement('table');
            details.classList.add(csscls('params'));
            details.style.display = 'none';

            const isEmptyObject = (obj) => {
                return !obj || (typeof obj === 'object' && Object.keys(obj).length === 0);
            };

            if (statement.bindings && !isEmptyObject(statement.bindings)) {
                details.append(this.renderDetailStrings('Bindings', 'thumb-tack', statement.bindings, true));
            }
            if (statement.hints && !isEmptyObject(statement.hints)) {
                details.append(this.renderDetailStrings('Hints', 'question-circle', statement.hints));
            }
            if (statement.backtrace && !isEmptyObject(statement.backtrace)) {
                details.append(this.renderDetailBacktrace('Backtrace', 'list-ul', statement.backtrace));
            }
            if (statement.explain && ['mariadb', 'mysql'].includes(statement.explain.driver)) {
                details.append(this.renderDetailExplain('Performance', 'tachometer', statement, this.explainMysql.bind(this)));
            }
            if (statement.explain && statement.explain.driver === 'pgsql') {
                details.append(this.renderDetailExplain('Performance', 'tachometer', statement, this.explainPgsql.bind(this)));
            }

            if (details.children.length) {
                li.classList.add(csscls('expandable'));
                li.addEventListener('click', (event) => {
                    if (window.getSelection().type === 'Range') {
                        return;
                    }

                    if (event.target.closest(`.${csscls('params')}`)) {
                        return;
                    }

                    const paramsTable = li.querySelector(`.${csscls('params')}`);
                    if (paramsTable && paramsTable.style.display !== 'none') {
                        paramsTable.style.display = 'none';
                    } else if (paramsTable) {
                        paramsTable.style.display = 'table';
                    }
                });
            }

            li.append(details);
        }

        renderDetail(caption, icon, value) {
            const tr = document.createElement('tr');
            const nameTd = document.createElement('td');
            nameTd.classList.add(csscls('name'));
            nameTd.innerHTML = caption + (icon ? `<i class="${css('text-muted')} fa fa-${icon}" />` : '');
            const valueTd = document.createElement('td');
            valueTd.classList.add(csscls('value'));
            if (typeof value === 'string') {
                valueTd.innerHTML = value;
            } else {
                valueTd.append(value);
            }
            tr.append(nameTd, valueTd);
            return tr;
        }

        renderDetailStrings(caption, icon, values, showLineNumbers = false) {
            const ul = document.createElement('ul');
            ul.classList.add(csscls('table-list'));

            Object.entries(values).forEach(([i, value]) => {
                const li = document.createElement('li');
                li.classList.add(csscls('table-list-item'));

                if (showLineNumbers) {
                    const muted = document.createElement('span');
                    muted.classList.add(css('text-muted'));
                    muted.textContent = `${i}:`;
                    const valueSpan = document.createElement('span');
                    valueSpan.textContent = value;
                    li.append(muted, '\u00A0', valueSpan);
                } else {
                    if (caption === 'Hints') {
                        li.innerHTML = value;
                    } else {
                        li.textContent = value;
                    }
                }
                ul.append(li);
            });

            return this.renderDetail(caption, icon, ul);
        }

        renderDetailBacktrace(caption, icon, traces) {
            const values = [];
            for (const trace of traces.values()) {
                let text = trace.name || trace.file;
                if (trace.namespace) {
                    text = `${trace.namespace}::${text}`;
                }
                if (trace.line) {
                    text = `${text}:${trace.line}`;
                }
                values.push(text);
            }

            return this.renderDetailStrings(caption, icon, values);
        }

        renderDetailExplain(caption, icon, statement, explainFn) {
            const btn = document.createElement('button');
            btn.textContent = 'Run EXPLAIN';
            btn.classList.add(csscls('explain-btn'));

            const detail = this.renderDetail(caption, icon, btn);

            btn.addEventListener('click', () => {
                fetch(statement.explain.url, {
                    method: 'POST',
                    body: JSON.stringify({
                        connection: statement.explain.connection,
                        query: statement.explain.query,
                        bindings: statement.bindings,
                        hash: statement.explain.hash
                    })
                }).then((response) => {
                    response.json()
                        .then((json) => {
                            if (!response.ok)
                                return alert(json.message); // eslint-disable-line no-alert
                            const valueCell = detail.querySelector(`.${csscls('value')}`);
                            valueCell.innerHTML = '';
                            explainFn(valueCell, statement, json.data, json.visual);
                        })
                        .catch(err => alert(`Response body could not be parsed. (${err})`)); // eslint-disable-line no-alert
                }).catch((e) => {
                    alert(e.message); // eslint-disable-line no-alert
                });
            });

            return detail;
        }
    }

    PhpDebugBar.Widgets.LaravelQueriesWidget = LaravelQueriesWidget;
})();
