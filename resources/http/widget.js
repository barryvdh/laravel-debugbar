(function () {
    const csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for the displaying Http Events
     *
     * Options:
     *  - data
     */
    class LaravelHttpWidget extends PhpDebugBar.Widget {
        get className() {
            return csscls('httpclient');
        }

        render() {
            this.list = new PhpDebugBar.Widgets.ListWidget({ itemRenderer(li, request) {
                // Create table row structure
                const table = document.createElement('div');
                table.classList.add(csscls('request-summary'));
                table.style.display = 'flex';
                table.style.gap = '10px';
                table.style.alignItems = 'center';

                // METHOD
                const method = document.createElement('span');
                method.classList.add(csscls('method'));
                method.textContent = request.method;
                method.style.fontWeight = 'bold';
                method.style.minWidth = '60px';
                table.append(method);

                // URL
                const url = document.createElement('span');
                url.classList.add(csscls('url'));
                url.textContent = request.url;
                url.style.flex = '1';
                url.style.overflow = 'hidden';
                url.style.textOverflow = 'ellipsis';
                url.style.whiteSpace = 'nowrap';
                table.append(url);

                // STATUS
                const status = document.createElement('span');
                status.classList.add(csscls('status'));
                status.textContent = request.status;
                status.style.minWidth = '40px';
                status.style.textAlign = 'center';
                // Color code status
                if (typeof request.status === 'number') {
                    if (request.status >= 200 && request.status < 300) {
                        status.style.color = '#4caf50';
                    } else if (request.status >= 300 && request.status < 400) {
                        status.style.color = '#ff9800';
                    } else if (request.status >= 400) {
                        status.style.color = '#f44336';
                    }
                }
                table.append(status);

                // DURATION
                if (request.duration !== null && typeof request.duration !== 'undefined') {
                    const duration = document.createElement('span');
                    duration.classList.add(csscls('duration'));
                    duration.textContent = request.duration + 'ms';
                    duration.style.minWidth = '60px';
                    duration.style.textAlign = 'right';
                    table.append(duration);
                }

                li.append(table);

                // Params section (expandable)
                if (request.params && Object.keys(request.params).length > 0) {
                    const paramsTable = document.createElement('table');
                    paramsTable.classList.add(csscls('params'));
                    const thead = document.createElement('thead');
                    thead.innerHTML = '<tr><th colspan="2">Params</th></tr>';
                    const tbody = document.createElement('tbody');
                    paramsTable.append(thead, tbody);

                    for (const key in request.params) {
                        if (typeof request.params[key] !== 'function') {
                            const row = document.createElement('tr');
                            row.innerHTML = `<td class="${csscls('name')}">${key}</td><td class="${csscls('value')}"><pre><code>${request.params[key]}</code></pre></td>`;
                            tbody.append(row);
                        }
                    }
                    paramsTable.hidden = true;
                    li.append(paramsTable);
                    li.style.cursor = 'pointer';
                    li.addEventListener('click', (event) => {
                        if (window.getSelection().type === 'Range' || event.target.closest('.sf-dump')) {
                            return;
                        }
                        paramsTable.hidden = !paramsTable.hidden;
                    });
                }
            } });

            this.el.append(this.list.el);

            this.bindAttr('data', function (data) {
                this.list.set('data', data);
            });
        }
    }

    PhpDebugBar.Widgets.LaravelHttpWidget = LaravelHttpWidget;
})();
