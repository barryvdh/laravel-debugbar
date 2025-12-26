(function () {
    const csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for the displaying cache events
     *
     * Options:
     *  - data
     */
    class LaravelCacheWidget extends PhpDebugBar.Widgets.TimelineWidget {
        get tagName() {
            return 'ul';
        }

        get className() {
            return csscls('timeline cache');
        }

        onForgetClick(e, el) {
            e.stopPropagation();

            const openhandler = this.debugbar.getOpenHandler();
            if (openhandler) {
                const params = JSON.parse(el.getAttribute('data-params'));
                openhandler.executeAction('cache', params.action, params.signature, params.payload,  (response) => {
                    if (response.ok) {
                        el.style.transition = 'opacity 200ms';
                        el.style.opacity = '0';
                        setTimeout(() => el.remove(), 200);
                    }
                });
            }

        }

        render() {
            super.render();

            this.bindAttr('data', function (data) {
                if (data.measures) {
                    const lines = this.el.querySelectorAll(`.${csscls('measure')}`);

                    for (let i = 0; i < data.measures.length; i++) {
                        const measure = data.measures[i];
                        const m = lines[i];

                        if (measure.params && Object.keys(measure.params).length > 0) {
                            if (measure.params.delete) {
                                const nextElement = m.nextElementSibling;
                                if (nextElement) {
                                    const deleteRow = Array.from(nextElement.querySelectorAll('td.phpdebugbar-widgets-name'))
                                        .find(td => td.textContent.includes('delete'));
                                    if (deleteRow) {
                                        deleteRow.closest('tr')?.remove();
                                    }
                                }
                            }
                            if (measure.params.delete && measure.params.key) {
                                const forgetLink = document.createElement('a');
                                forgetLink.className = csscls('forget');
                                forgetLink.textContent = 'forget';
                                forgetLink.setAttribute('data-params', measure.params.delete);
                                forgetLink.addEventListener('click', (e) => {
                                    this.onForgetClick(e, forgetLink);
                                }, { once: true });
                                m.appendChild(forgetLink);
                            }
                        }
                    }
                }
            });
        }
    }

    PhpDebugBar.Widgets.LaravelCacheWidget = LaravelCacheWidget;
})();
