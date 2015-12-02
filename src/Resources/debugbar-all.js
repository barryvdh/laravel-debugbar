define(['jquery'], function($){
    if (typeof(PhpDebugBar) == 'undefined') {
        // namespace
        var PhpDebugBar = {};
        PhpDebugBar.$ = jQuery;
    }

    (function($) {

        if (typeof(localStorage) == 'undefined') {
            // provide mock localStorage object for dumb browsers
            localStorage = {
                setItem: function(key, value) {},
                getItem: function(key) { return null; }
            };
        }

        if (typeof(PhpDebugBar.utils) == 'undefined') {
            PhpDebugBar.utils = {};
        }

        /**
         * Returns the value from an object property.
         * Using dots in the key, it is possible to retrieve nested property values
         *
         * @param {Object} dict
         * @param {String} key
         * @param {Object} default_value
         * @return {Object}
         */
        var getDictValue = PhpDebugBar.utils.getDictValue = function(dict, key, default_value) {
            var d = dict, parts = key.split('.');
            for (var i = 0; i < parts.length; i++) {
                if (!d[parts[i]]) {
                    return default_value;
                }
                d = d[parts[i]];
            }
            return d;
        }

        /**
         * Counts the number of properties in an object
         *
         * @param {Object} obj
         * @return {Integer}
         */
        var getObjectSize = PhpDebugBar.utils.getObjectSize = function(obj) {
            if (Object.keys) {
                return Object.keys(obj).length;
            }
            var count = 0;
            for (var k in obj) {
                if (obj.hasOwnProperty(k)) {
                    count++;
                }
            }
            return count;
        }

        /**
         * Returns a prefixed css class name
         *
         * @param {String} cls
         * @return {String}
         */
        PhpDebugBar.utils.csscls = function(cls, prefix) {
            if (cls.indexOf(' ') > -1) {
                var clss = cls.split(' '), out = [];
                for (var i = 0, c = clss.length; i < c; i++) {
                    out.push(PhpDebugBar.utils.csscls(clss[i], prefix));
                }
                return out.join(' ');
            }
            if (cls.indexOf('.') === 0) {
                return '.' + prefix + cls.substr(1);
            }
            return prefix + cls;
        };

        /**
         * Creates a partial function of csscls where the second
         * argument is already defined
         *
         * @param  {string} prefix
         * @return {Function}
         */
        PhpDebugBar.utils.makecsscls = function(prefix) {
            var f = function(cls) {
                return PhpDebugBar.utils.csscls(cls, prefix);
            };
            return f;
        }

        var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-');


        // ------------------------------------------------------------------

        /**
         * Base class for all elements with a visual component
         *
         * @param {Object} options
         * @constructor
         */
        var Widget = PhpDebugBar.Widget = function(options) {
            this._attributes = $.extend({}, this.defaults);
            this._boundAttributes = {};
            this.$el = $('<' + this.tagName + ' />');
            if (this.className) {
                this.$el.addClass(this.className);
            }
            this.initialize.apply(this, [options || {}]);
            this.render.apply(this);
        };

        $.extend(Widget.prototype, {

            tagName: 'div',

            className: null,

            defaults: {},

            /**
             * Called after the constructor
             *
             * @param {Object} options
             */
            initialize: function(options) {
                this.set(options);
            },

            /**
             * Called after the constructor to render the element
             */
            render: function() {},

            /**
             * Sets the value of an attribute
             *
             * @param {String} attr Can also be an object to set multiple attributes at once
             * @param {Object} value
             */
            set: function(attr, value) {
                if (typeof(attr) != 'string') {
                    for (var k in attr) {
                        this.set(k, attr[k]);
                    }
                    return;
                }

                this._attributes[attr] = value;
                if (typeof(this._boundAttributes[attr]) !== 'undefined') {
                    for (var i = 0, c = this._boundAttributes[attr].length; i < c; i++) {
                        this._boundAttributes[attr][i].apply(this, [value]);
                    }
                }
            },

            /**
             * Checks if an attribute exists and is not null
             *
             * @param {String} attr
             * @return {[type]} [description]
             */
            has: function(attr) {
                return typeof(this._attributes[attr]) !== 'undefined' && this._attributes[attr] !== null;
            },

            /**
             * Returns the value of an attribute
             *
             * @param {String} attr
             * @return {Object}
             */
            get: function(attr) {
                return this._attributes[attr];
            },

            /**
             * Registers a callback function that will be called whenever the value of the attribute changes
             *
             * If cb is a jQuery element, text() will be used to fill the element
             *
             * @param {String} attr
             * @param {Function} cb
             */
            bindAttr: function(attr, cb) {
                if ($.isArray(attr)) {
                    for (var i = 0, c = attr.length; i < c; i++) {
                        this.bindAttr(attr[i], cb);
                    }
                    return;
                }

                if (typeof(this._boundAttributes[attr]) == 'undefined') {
                    this._boundAttributes[attr] = [];
                }
                if (typeof(cb) == 'object') {
                    var el = cb;
                    cb = function(value) { el.text(value || ''); };
                }
                this._boundAttributes[attr].push(cb);
                if (this.has(attr)) {
                    cb.apply(this, [this._attributes[attr]]);
                }
            }

        });


        /**
         * Creates a subclass
         *
         * Code from Backbone.js
         *
         * @param {Array} props Prototype properties
         * @return {Function}
         */
        Widget.extend = function(props) {
            var parent = this;

            var child = function() { return parent.apply(this, arguments); };
            $.extend(child, parent);

            var Surrogate = function(){ this.constructor = child; };
            Surrogate.prototype = parent.prototype;
            child.prototype = new Surrogate;
            $.extend(child.prototype, props);

            child.__super__ = parent.prototype;

            return child;
        };

        // ------------------------------------------------------------------

        /**
         * Tab
         *
         * A tab is composed of a tab label which is always visible and
         * a tab panel which is visible only when the tab is active.
         *
         * The panel must contain a widget. A widget is an object which has
         * an element property containing something appendable to a jQuery object.
         *
         * Options:
         *  - title
         *  - badge
         *  - widget
         *  - data: forward data to widget data
         */
        var Tab = Widget.extend({

            className: csscls('panel'),

            render: function() {
                this.$tab = $('<a />').addClass(csscls('tab'));

                this.$icon = $('<i />').appendTo(this.$tab);
                this.bindAttr('icon', function(icon) {
                    if (icon) {
                        this.$icon.attr('class', 'fa fa-' + icon);
                    } else {
                        this.$icon.attr('class', '');
                    }
                });

                this.bindAttr('title', $('<span />').addClass(csscls('text')).appendTo(this.$tab));

                this.$badge = $('<span />').addClass(csscls('badge')).appendTo(this.$tab);
                this.bindAttr('badge', function(value) {
                    if (value !== null) {
                        this.$badge.text(value);
                        this.$badge.show();
                    } else {
                        this.$badge.hide();
                    }
                });

                this.bindAttr('widget', function(widget) {
                    this.$el.empty().append(widget.$el);
                });

                this.bindAttr('data', function(data) {
                    if (this.has('widget')) {
                        this.get('widget').set('data', data);
                    }
                })
            }

        });

        // ------------------------------------------------------------------

        /**
         * Indicator
         *
         * An indicator is a text and an icon to display single value information
         * right inside the always visible part of the debug bar
         *
         * Options:
         *  - icon
         *  - title
         *  - tooltip
         *  - data: alias of title
         */
        var Indicator = Widget.extend({

            tagName: 'span',

            className: csscls('indicator'),

            render: function() {
                this.$icon = $('<i />').appendTo(this.$el);
                this.bindAttr('icon', function(icon) {
                    if (icon) {
                        this.$icon.attr('class', 'fa fa-' + icon);
                    } else {
                        this.$icon.attr('class', '');
                    }
                });

                this.bindAttr(['title', 'data'], $('<span />').addClass(csscls('text')).appendTo(this.$el));

                this.$tooltip = $('<span />').addClass(csscls('tooltip disabled')).appendTo(this.$el);
                this.bindAttr('tooltip', function(tooltip) {
                    if (tooltip) {
                        this.$tooltip.text(tooltip).removeClass(csscls('disabled'));
                    } else {
                        this.$tooltip.addClass(csscls('disabled'));
                    }
                });
            }

        });

        // ------------------------------------------------------------------

        /**
         * Dataset title formater
         *
         * Formats the title of a dataset for the select box
         */
        var DatasetTitleFormater = PhpDebugBar.DatasetTitleFormater = function(debugbar) {
            this.debugbar = debugbar;
        };

        $.extend(DatasetTitleFormater.prototype, {

            /**
             * Formats the title of a dataset
             *
             * @this {DatasetTitleFormater}
             * @param {String} id
             * @param {Object} data
             * @param {String} suffix
             * @return {String}
             */
            format: function(id, data, suffix) {
                if (suffix) {
                    suffix = ' ' + suffix;
                } else {
                    suffix = '';
                }

                var nb = getObjectSize(this.debugbar.datasets) + 1;

                if (typeof(data['__meta']) === 'undefined') {
                    return "#" + nb + suffix;
                }

                var filename = data['__meta']['uri'].substr(data['__meta']['uri'].lastIndexOf('/') + 1);
                var label = "#" + nb + " " + filename + suffix + ' (' + data['__meta']['datetime'].split(' ')[1] + ')';
                return label;
            }

        });

        // ------------------------------------------------------------------


        /**
         * DebugBar
         *
         * Creates a bar that appends itself to the body of your page
         * and sticks to the bottom.
         *
         * The bar can be customized by adding tabs and indicators.
         * A data map is used to fill those controls with data provided
         * from datasets.
         */
        var DebugBar = PhpDebugBar.DebugBar = Widget.extend({

            className: "phpdebugbar " + csscls('minimized'),

            options: {
                bodyPaddingBottom: true
            },

            initialize: function() {
                this.controls = {};
                this.dataMap = {};
                this.datasets = {};
                this.firstTabName = null;
                this.activePanelName = null;
                this.datesetTitleFormater = new DatasetTitleFormater(this);
                this.registerResizeHandler();
            },

            /**
             * Register resize event, for resize debugbar with reponsive css.
             *
             * @this {DebugBar}
             */
            registerResizeHandler: function() {
                if (typeof this.resize.bind == 'undefined') return;

                var f = this.resize.bind(this);
                this.respCSSSize = 0;
                $(window).resize(f);
                setTimeout(f, 20);
            },

            /**
             * Resizes the debugbar to fit the current browser window
             */
            resize: function() {
                var contentSize = this.respCSSSize;
                if (this.respCSSSize == 0) {
                    this.$header.find("> div > *:visible").each(function () {
                        contentSize += $(this).outerWidth();
                    });
                }

                var currentSize = this.$header.width();
                var cssClass = "phpdebugbar-mini-design";
                var bool = this.$header.hasClass(cssClass);

                if (currentSize <= contentSize && !bool) {
                    this.respCSSSize = contentSize;
                    this.$header.addClass(cssClass);
                } else if (contentSize < currentSize && bool) {
                    this.respCSSSize = 0;
                    this.$header.removeClass(cssClass);
                }

                // Reset height to ensure bar is still visible
                this.setHeight(this.$body.height());
            },

            /**
             * Initialiazes the UI
             *
             * @this {DebugBar}
             */
            render: function() {
                var self = this;
                this.$el.appendTo('body');
                this.$dragCapture = $('<div />').addClass(csscls('drag-capture')).appendTo(this.$el);
                this.$resizehdle = $('<div />').addClass(csscls('resize-handle')).appendTo(this.$el);
                this.$header = $('<div />').addClass(csscls('header')).appendTo(this.$el);
                this.$headerLeft = $('<div />').addClass(csscls('header-left')).appendTo(this.$header);
                this.$headerRight = $('<div />').addClass(csscls('header-right')).appendTo(this.$header);
                var $body = this.$body = $('<div />').addClass(csscls('body')).appendTo(this.$el);
                this.recomputeBottomOffset();

                // dragging of resize handle
                var pos_y, orig_h;
                this.$resizehdle.on('mousedown', function(e) {
                    orig_h = $body.height(), pos_y = e.pageY;
                    $body.parents().on('mousemove', mousemove).on('mouseup', mouseup);
                    self.$dragCapture.show();
                    e.preventDefault();
                });
                var mousemove = function(e) {
                    var h = orig_h + (pos_y - e.pageY);
                    self.setHeight(h);
                };
                var mouseup = function() {
                    $body.parents().off('mousemove', mousemove).off('mouseup', mouseup);
                    self.$dragCapture.hide();
                };

                // close button
                this.$closebtn = $('<a />').addClass(csscls('close-btn')).appendTo(this.$headerRight);
                this.$closebtn.click(function() {
                    self.close();
                });

                // minimize button
                this.$minimizebtn = $('<a />').addClass(csscls('minimize-btn') ).appendTo(this.$headerRight);
                this.$minimizebtn.click(function() {
                    self.minimize();
                });

                // maximize button
                this.$maximizebtn = $('<a />').addClass(csscls('maximize-btn') ).appendTo(this.$headerRight);
                this.$maximizebtn.click(function() {
                    self.restore();
                });

                // restore button
                this.$restorebtn = $('<a />').addClass(csscls('restore-btn')).hide().appendTo(this.$el);
                this.$restorebtn.click(function() {
                    self.restore();
                });

                // open button
                this.$openbtn = $('<a />').addClass(csscls('open-btn')).appendTo(this.$headerRight).hide();
                this.$openbtn.click(function() {
                    self.openHandler.show(function(id, dataset) {
                        self.addDataSet(dataset, id, "(opened)");
                        self.showTab();
                    });
                });

                // select box for data sets
                this.$datasets = $('<select />').addClass(csscls('datasets-switcher')).appendTo(this.$headerRight);
                this.$datasets.change(function() {
                    self.dataChangeHandler(self.datasets[this.value]);
                    self.showTab();
                });
            },

            /**
             * Sets the height of the debugbar body section
             * Forces the height to lie within a reasonable range
             * Stores the height in local storage so it can be restored
             * Resets the document body bottom offset
             *
             * @this {DebugBar}
             */
            setHeight: function(height) {
                var min_h = 40;
                var max_h = $(window).innerHeight() - this.$header.height() - 10;
                height = Math.min(height, max_h);
                height = Math.max(height, min_h);
                this.$body.css('height', height);
                localStorage.setItem('phpdebugbar-height', height);
                this.recomputeBottomOffset();
            },

            /**
             * Restores the state of the DebugBar using localStorage
             * This is not called by default in the constructor and
             * needs to be called by subclasses in their init() method
             *
             * @this {DebugBar}
             */
            restoreState: function() {
                // bar height
                var height = localStorage.getItem('phpdebugbar-height');
                this.setHeight(height || this.$body.height());

                // bar visibility
                var open = localStorage.getItem('phpdebugbar-open');
                if (open && open == '0') {
                    this.close();
                } else {
                    var visible = localStorage.getItem('phpdebugbar-visible');
                    if (visible && visible == '1') {
                        var tab = localStorage.getItem('phpdebugbar-tab');
                        if (this.isTab(tab)) {
                            this.showTab(tab);
                        }
                    }
                }
            },

            /**
             * Creates and adds a new tab
             *
             * @this {DebugBar}
             * @param {String} name Internal name
             * @param {Object} widget A widget object with an element property
             * @param {String} title The text in the tab, if not specified, name will be used
             * @return {Tab}
             */
            createTab: function(name, widget, title) {
                var tab = new Tab({
                    title: title || (name.replace(/[_\-]/g, ' ').charAt(0).toUpperCase() + name.slice(1)),
                    widget: widget
                });
                return this.addTab(name, tab);
            },

            /**
             * Adds a new tab
             *
             * @this {DebugBar}
             * @param {String} name Internal name
             * @param {Tab} tab Tab object
             * @return {Tab}
             */
            addTab: function(name, tab) {
                if (this.isControl(name)) {
                    throw new Error(name + ' already exists');
                }

                var self = this;
                tab.$tab.appendTo(this.$headerLeft).click(function() {
                    if (!self.isMinimized() && self.activePanelName == name) {
                        self.minimize();
                    } else {
                        self.showTab(name);
                    }
                });
                tab.$el.appendTo(this.$body);

                this.controls[name] = tab;
                if (this.firstTabName == null) {
                    this.firstTabName = name;
                }
                return tab;
            },

            /**
             * Creates and adds an indicator
             *
             * @this {DebugBar}
             * @param {String} name Internal name
             * @param {String} icon
             * @param {String} tooltip
             * @param {String} position "right" or "left", default is "right"
             * @return {Indicator}
             */
            createIndicator: function(name, icon, tooltip, position) {
                var indicator = new Indicator({
                    icon: icon,
                    tooltip: tooltip
                });
                return this.addIndicator(name, indicator, position);
            },

            /**
             * Adds an indicator
             *
             * @this {DebugBar}
             * @param {String} name Internal name
             * @param {Indicator} indicator Indicator object
             * @return {Indicator}
             */
            addIndicator: function(name, indicator, position) {
                if (this.isControl(name)) {
                    throw new Error(name + ' already exists');
                }

                if (position == 'left') {
                    indicator.$el.insertBefore(this.$headerLeft.children().first());
                } else {
                    indicator.$el.appendTo(this.$headerRight);
                }

                this.controls[name] = indicator;
                return indicator;
            },

            /**
             * Returns a control
             *
             * @param {String} name
             * @return {Object}
             */
            getControl: function(name) {
                if (this.isControl(name)) {
                    return this.controls[name];
                }
            },

            /**
             * Checks if there's a control under the specified name
             *
             * @this {DebugBar}
             * @param {String} name
             * @return {Boolean}
             */
            isControl: function(name) {
                return typeof(this.controls[name]) != 'undefined';
            },

            /**
             * Checks if a tab with the specified name exists
             *
             * @this {DebugBar}
             * @param {String} name
             * @return {Boolean}
             */
            isTab: function(name) {
                return this.isControl(name) && this.controls[name] instanceof Tab;
            },

            /**
             * Checks if an indicator with the specified name exists
             *
             * @this {DebugBar}
             * @param {String} name
             * @return {Boolean}
             */
            isIndicator: function(name) {
                return this.isControl(name) && this.controls[name] instanceof Indicator;
            },

            /**
             * Removes all tabs and indicators from the debug bar and hides it
             *
             * @this {DebugBar}
             */
            reset: function() {
                this.minimize();
                var self = this;
                $.each(this.controls, function(name, control) {
                    if (self.isTab(name)) {
                        control.$tab.remove();
                    }
                    control.$el.remove();
                });
                this.controls = {};
            },

            /**
             * Open the debug bar and display the specified tab
             *
             * @this {DebugBar}
             * @param {String} name If not specified, display the first tab
             */
            showTab: function(name) {
                if (!name) {
                    if (this.activePanelName) {
                        name = this.activePanelName;
                    } else {
                        name = this.firstTabName;
                    }
                }

                if (!this.isTab(name)) {
                    throw new Error("Unknown tab '" + name + "'");
                }

                this.$resizehdle.show();
                this.$body.show();
                this.recomputeBottomOffset();

                $(this.$header).find('> div > .' + csscls('active')).removeClass(csscls('active'));
                $(this.$body).find('> .' + csscls('active')).removeClass(csscls('active'));

                this.controls[name].$tab.addClass(csscls('active'));
                this.controls[name].$el.addClass(csscls('active'));
                this.activePanelName = name;

                this.$el.removeClass(csscls('minimized'));
                localStorage.setItem('phpdebugbar-visible', '1');
                localStorage.setItem('phpdebugbar-tab', name);
                this.resize();
            },

            /**
             * Hide panels and minimize the debug bar
             *
             * @this {DebugBar}
             */
            minimize: function() {
                this.$header.find('> div > .' + csscls('active')).removeClass(csscls('active'));
                this.$body.hide();
                this.$resizehdle.hide();
                this.recomputeBottomOffset();
                localStorage.setItem('phpdebugbar-visible', '0');
                this.$el.addClass(csscls('minimized'));
                this.resize();
            },

            /**
             * Checks if the panel is minimized
             *
             * @return {Boolean}
             */
            isMinimized: function() {
                return this.$el.hasClass(csscls('minimized'));
            },

            /**
             * Close the debug bar
             *
             * @this {DebugBar}
             */
            close: function() {
                this.$resizehdle.hide();
                this.$header.hide();
                this.$body.hide();
                this.$restorebtn.show();
                localStorage.setItem('phpdebugbar-open', '0');
                this.$el.addClass(csscls('closed'));
                this.recomputeBottomOffset();
            },

            /**
             * Restore the debug bar
             *
             * @this {DebugBar}
             */
            restore: function() {
                this.$resizehdle.show();
                this.$header.show();
                this.$restorebtn.hide();
                localStorage.setItem('phpdebugbar-open', '1');
                var tab = localStorage.getItem('phpdebugbar-tab');
                if (this.isTab(tab)) {
                    this.showTab(tab);
                }
                this.$el.removeClass(csscls('closed'));
                this.resize();
            },

            /**
             * Recomputes the padding-bottom css property of the body so
             * that the debug bar never hides any content
             */
            recomputeBottomOffset: function() {
                if (this.options.bodyPaddingBottom) {
                    $('body').css('padding-bottom', this.$el.height());
                }
            },

            /**
             * Sets the data map used by dataChangeHandler to populate
             * indicators and widgets
             *
             * A data map is an object where properties are control names.
             * The value of each property should be an array where the first
             * item is the name of a property from the data object (nested properties
             * can be specified) and the second item the default value.
             *
             * Example:
             *     {"memory": ["memory.peak_usage_str", "0B"]}
             *
             * @this {DebugBar}
             * @param {Object} map
             */
            setDataMap: function(map) {
                this.dataMap = map;
            },

            /**
             * Same as setDataMap() but appends to the existing map
             * rather than replacing it
             *
             * @this {DebugBar}
             * @param {Object} map
             */
            addDataMap: function(map) {
                $.extend(this.dataMap, map);
            },

            /**
             * Resets datasets and add one set of data
             *
             * For this method to be usefull, you need to specify
             * a dataMap using setDataMap()
             *
             * @this {DebugBar}
             * @param {Object} data
             * @return {String} Dataset's id
             */
            setData: function(data) {
                this.datasets = {};
                return this.addDataSet(data);
            },

            /**
             * Adds a dataset
             *
             * If more than one dataset are added, the dataset selector
             * will be displayed.
             *
             * For this method to be usefull, you need to specify
             * a dataMap using setDataMap()
             *
             * @this {DebugBar}
             * @param {Object} data
             * @param {String} id The name of this set, optional
             * @param {String} suffix
             * @return {String} Dataset's id
             */
            addDataSet: function(data, id, suffix) {
                var label = this.datesetTitleFormater.format(id, data, suffix);
                id = id || (getObjectSize(this.datasets) + 1);
                this.datasets[id] = data;

                this.$datasets.append($('<option value="' + id + '">' + label + '</option>'));
                if (this.$datasets.children().length > 1) {
                    this.$datasets.show();
                }

                this.showDataSet(id);
                return id;
            },

            /**
             * Loads a dataset using the open handler
             *
             * @param {String} id
             */
            loadDataSet: function(id, suffix, callback) {
                if (!this.openHandler) {
                    throw new Error('loadDataSet() needs an open handler');
                }
                var self = this;
                this.openHandler.load(id, function(data) {
                    self.addDataSet(data, id, suffix);
                    callback && callback(data);
                });
            },

            /**
             * Returns the data from a dataset
             *
             * @this {DebugBar}
             * @param {String} id
             * @return {Object}
             */
            getDataSet: function(id) {
                return this.datasets[id];
            },

            /**
             * Switch the currently displayed dataset
             *
             * @this {DebugBar}
             * @param {String} id
             */
            showDataSet: function(id) {
                this.dataChangeHandler(this.datasets[id]);
                this.$datasets.val(id);
            },

            /**
             * Called when the current dataset is modified.
             *
             * @this {DebugBar}
             * @param {Object} data
             */
            dataChangeHandler: function(data) {
                var self = this;
                $.each(this.dataMap, function(key, def) {
                    var d = getDictValue(data, def[0], def[1]);
                    if (key.indexOf(':') != -1) {
                        key = key.split(':');
                        self.getControl(key[0]).set(key[1], d);
                    } else {
                        self.getControl(key).set('data', d);
                    }
                });
            },

            /**
             * Sets the handler to open past dataset
             *
             * @this {DebugBar}
             * @param {object} handler
             */
            setOpenHandler: function(handler) {
                this.openHandler = handler;
                if (handler !== null) {
                    this.$openbtn.show();
                } else {
                    this.$openbtn.hide();
                }
            },

            /**
             * Returns the handler to open past dataset
             *
             * @this {DebugBar}
             * @return {object}
             */
            getOpenHandler: function() {
                return this.openHandler;
            }

        });

        DebugBar.Tab = Tab;
        DebugBar.Indicator = Indicator;

        // ------------------------------------------------------------------

        /**
         * AjaxHandler
         *
         * Extract data from headers of an XMLHttpRequest and adds a new dataset
         */
        var AjaxHandler = PhpDebugBar.AjaxHandler = function(debugbar, headerName) {
            this.debugbar = debugbar;
            this.headerName = headerName || 'phpdebugbar';
        };

        $.extend(AjaxHandler.prototype, {

            /**
             * Handles an XMLHttpRequest
             *
             * @this {AjaxHandler}
             * @param {XMLHttpRequest} xhr
             * @return {Bool}
             */
            handle: function(xhr) {
                if (!this.loadFromId(xhr)) {
                    return this.loadFromData(xhr);
                }
                return true;
            },

            /**
             * Checks if the HEADER-id exists and loads the dataset using the open handler
             *
             * @param {XMLHttpRequest} xhr
             * @return {Bool}
             */
            loadFromId: function(xhr) {
                var id = this.extractIdFromHeaders(xhr);
                if (id && this.debugbar.openHandler) {
                    this.debugbar.loadDataSet(id, "(ajax)");
                    return true;
                }
                return false;
            },

            /**
             * Extracts the id from the HEADER-id
             *
             * @param {XMLHttpRequest} xhr
             * @return {String}
             */
            extractIdFromHeaders: function(xhr) {
                return xhr.getResponseHeader(this.headerName + '-id');
            },

            /**
             * Checks if the HEADER exists and loads the dataset
             *
             * @param {XMLHttpRequest} xhr
             * @return {Bool}
             */
            loadFromData: function(xhr) {
                var raw = this.extractDataFromHeaders(xhr);
                if (!raw) {
                    return false;
                }

                var data = this.parseHeaders(raw);
                if (data.error) {
                    throw new Error('Error loading debugbar data: ' + data.error);
                } else if(data.data) {
                    this.debugbar.addDataSet(data.data, data.id, "(ajax)");
                }
                return true;
            },

            /**
             * Extract the data as a string from headers of an XMLHttpRequest
             *
             * @this {AjaxHandler}
             * @param {XMLHttpRequest} xhr
             * @return {string}
             */
            extractDataFromHeaders: function(xhr) {
                var data = xhr.getResponseHeader(this.headerName);
                if (!data) {
                    return;
                }
                for (var i = 1;; i++) {
                    var header = xhr.getResponseHeader(this.headerName + '-' + i);
                    if (!header) {
                        break;
                    }
                    data += header;
                }
                return decodeURIComponent(data);
            },

            /**
             * Parses the string data into an object
             *
             * @this {AjaxHandler}
             * @param {string} data
             * @return {string}
             */
            parseHeaders: function(data) {
                return JSON.parse(data);
            },

            /**
             * Attaches an event listener to jQuery.ajaxComplete()
             *
             * @this {AjaxHandler}
             * @param {jQuery} jq Optional
             */
            bindToJquery: function(jq) {
                var self = this;
                jq(document).ajaxComplete(function(e, xhr, settings) {
                    if (!settings.ignoreDebugBarAjaxHandler) {
                        self.handle(xhr);
                    }
                });
            },

            /**
             * Attaches an event listener to XMLHttpRequest
             *
             * @this {AjaxHandler}
             */
            bindToXHR: function() {
                var self = this;
                var proxied = XMLHttpRequest.prototype.open;
                XMLHttpRequest.prototype.open = function(method, url, async, user, pass) {
                    var xhr = this;
                    this.addEventListener("readystatechange", function() {
                        var skipUrl = self.debugbar.openHandler ? self.debugbar.openHandler.get('url') : null;
                        if (xhr.readyState == 4 && url.indexOf(skipUrl) !== 0) {
                            self.handle(xhr);
                        }
                    }, false);
                    proxied.apply(this, Array.prototype.slice.call(arguments));
                };
            }

        });

    })(PhpDebugBar.$);

    if (typeof(PhpDebugBar) == 'undefined') {
        // namespace
        var PhpDebugBar = {};
        PhpDebugBar.$ = jQuery;
    }

    (function($) {

        /**
         * @namespace
         */
        PhpDebugBar.Widgets = {};

        /**
         * Replaces spaces with &nbsp; and line breaks with <br>
         *
         * @param {String} text
         * @return {String}
         */
        var htmlize = PhpDebugBar.Widgets.htmlize = function(text) {
            return text.replace(/\n/g, '<br>').replace(/\s/g, "&nbsp;")
        };

        /**
         * Returns a string representation of value, using JSON.stringify
         * if it's an object.
         *
         * @param {Object} value
         * @param {Boolean} prettify Uses htmlize() if true
         * @return {String}
         */
        var renderValue = PhpDebugBar.Widgets.renderValue = function(value, prettify) {
            if (typeof(value) !== 'string') {
                if (prettify) {
                    return htmlize(JSON.stringify(value, undefined, 2));
                }
                return JSON.stringify(value);
            }
            return value;
        };

        /**
         * Highlights a block of code
         *
         * @param  {String} code
         * @param  {String} lang
         * @return {String}
         */
        var highlight = PhpDebugBar.Widgets.highlight = function(code, lang) {
            if (typeof(code) === 'string') {
                if (typeof(hljs) === 'undefined') {
                    return htmlize(code);
                }
                if (lang) {
                    return hljs.highlight(lang, code).value;
                }
                return hljs.highlightAuto(code).value;
            }

            if (typeof(hljs) === 'object') {
                code.each(function(i, e) { hljs.highlightBlock(e); });
            }
            return code;
        };

        /**
         * Creates a <pre> element with a block of code
         *
         * @param  {String} code
         * @param  {String} lang
         * @return {String}
         */
        var createCodeBlock = PhpDebugBar.Widgets.createCodeBlock = function(code, lang) {
            var pre = $('<pre />');
            $('<code />').text(code).appendTo(pre);
            if (lang) {
                pre.addClass("language-" + lang);
            }
            highlight(pre);
            return pre;
        };

        var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');


        // ------------------------------------------------------------------
        // Generic widgets
        // ------------------------------------------------------------------

        /**
         * Displays array element in a <ul> list
         *
         * Options:
         *  - data
         *  - itemRenderer: a function used to render list items (optional)
         */
        var ListWidget = PhpDebugBar.Widgets.ListWidget = PhpDebugBar.Widget.extend({

            tagName: 'ul',

            className: csscls('list'),

            initialize: function(options) {
                if (!options['itemRenderer']) {
                    options['itemRenderer'] = this.itemRenderer;
                }
                this.set(options);
            },

            render: function() {
                this.bindAttr(['itemRenderer', 'data'], function() {
                    this.$el.empty();
                    if (!this.has('data')) {
                        return;
                    }

                    var data = this.get('data');
                    for (var i = 0; i < data.length; i++) {
                        var li = $('<li />').addClass(csscls('list-item')).appendTo(this.$el);
                        this.get('itemRenderer')(li, data[i]);
                    }
                });
            },

            /**
             * Renders the content of a <li> element
             *
             * @param {jQuery} li The <li> element as a jQuery Object
             * @param {Object} value An item from the data array
             */
            itemRenderer: function(li, value) {
                li.html(renderValue(value));
            }

        });

        // ------------------------------------------------------------------

        /**
         * Displays object property/value paris in a <dl> list
         *
         * Options:
         *  - data
         *  - itemRenderer: a function used to render list items (optional)
         */
        var KVListWidget = PhpDebugBar.Widgets.KVListWidget = ListWidget.extend({

            tagName: 'dl',

            className: csscls('kvlist'),

            render: function() {
                this.bindAttr(['itemRenderer', 'data'], function() {
                    this.$el.empty();
                    if (!this.has('data')) {
                        return;
                    }

                    var self = this;
                    $.each(this.get('data'), function(key, value) {
                        var dt = $('<dt />').addClass(csscls('key')).appendTo(self.$el);
                        var dd = $('<dd />').addClass(csscls('value')).appendTo(self.$el);
                        self.get('itemRenderer')(dt, dd, key, value);
                    });
                });
            },

            /**
             * Renders the content of the <dt> and <dd> elements
             *
             * @param {jQuery} dt The <dt> element as a jQuery Object
             * @param {jQuery} dd The <dd> element as a jQuery Object
             * @param {String} key Property name
             * @param {Object} value Property value
             */
            itemRenderer: function(dt, dd, key, value) {
                dt.text(key);
                dd.html(htmlize(value));
            }

        });

        // ------------------------------------------------------------------

        /**
         * An extension of KVListWidget where the data represents a list
         * of variables
         *
         * Options:
         *  - data
         */
        var VariableListWidget = PhpDebugBar.Widgets.VariableListWidget = KVListWidget.extend({

            className: csscls('kvlist varlist'),

            itemRenderer: function(dt, dd, key, value) {
                $('<span />').attr('title', key).text(key).appendTo(dt);

                var v = value;
                if (v && v.length > 100) {
                    v = v.substr(0, 100) + "...";
                }
                var prettyVal = null;
                dd.text(v).click(function() {
                    if (dd.hasClass(csscls('pretty'))) {
                        dd.text(v).removeClass(csscls('pretty'));
                    } else {
                        prettyVal = prettyVal || createCodeBlock(value);
                        dd.addClass(csscls('pretty')).empty().append(prettyVal);
                    }
                });
            }

        });

        // ------------------------------------------------------------------

        /**
         * Iframe widget
         *
         * Options:
         *  - data
         */
        var IFrameWidget = PhpDebugBar.Widgets.IFrameWidget = PhpDebugBar.Widget.extend({

            tagName: 'iframe',

            className: csscls('iframe'),

            render: function() {
                this.$el.attr({
                    seamless: "seamless",
                    border: "0",
                    width: "100%",
                    height: "100%"
                });
                this.bindAttr('data', function(url) { this.$el.attr('src', url); });
            }

        });


        // ------------------------------------------------------------------
        // Collector specific widgets
        // ------------------------------------------------------------------

        /**
         * Widget for the MessagesCollector
         *
         * Uses ListWidget under the hood
         *
         * Options:
         *  - data
         */
        var MessagesWidget = PhpDebugBar.Widgets.MessagesWidget = PhpDebugBar.Widget.extend({

            className: csscls('messages'),

            render: function() {
                var self = this;

                this.$list = new ListWidget({ itemRenderer: function(li, value) {
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
                        li.css('cursor', 'pointer').click(function() {
                            if (val.hasClass(csscls('pretty'))) {
                                val.text(m).removeClass(csscls('pretty'));
                            } else {
                                prettyVal = prettyVal || createCodeBlock(value.message, 'php');
                                val.addClass(csscls('pretty')).empty().append(prettyVal);
                            }
                        });
                    }

                    if (value.label) {
                        val.addClass(csscls(value.label));
                        $('<span />').addClass(csscls('label')).text(value.label).appendTo(li);
                    }
                    if (value.collector) {
                        $('<span />').addClass(csscls('collector')).text(value.collector).appendTo(li);
                    }
                }});

                this.$list.$el.appendTo(this.$el);
                this.$toolbar = $('<div><i class="fa fa-search"></i></div>').addClass(csscls('toolbar')).appendTo(this.$el);

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
                });

                this.bindAttr(['exclude', 'search'], function() {
                    var data = this.get('data'),
                        exclude = this.get('exclude'),
                        search = this.get('search'),
                        fdata = [];

                    for (var i = 0; i < data.length; i++) {
                        if ((!data[i].label || $.inArray(data[i].label, exclude) === -1) && (!search || data[i].message.indexOf(search) > -1)) {
                            fdata.push(data[i]);
                        }
                    }

                    this.$list.set('data', fdata);
                });
            },

            onFilterClick: function(el) {
                $(el).toggleClass(csscls('excluded'));

                var excludedLabels = [];
                this.$toolbar.find(csscls('.filter') + csscls('.excluded')).each(function() {
                    excludedLabels.push(this.rel);
                });

                this.set('exclude', excludedLabels);
            }

        });

        // ------------------------------------------------------------------

        /**
         * Widget for the TimeDataCollector
         *
         * Options:
         *  - data
         */
        var TimelineWidget = PhpDebugBar.Widgets.TimelineWidget = PhpDebugBar.Widget.extend({

            tagName: 'ul',

            className: csscls('timeline'),

            render: function() {
                this.bindAttr('data', function(data) {
                    this.$el.empty();
                    if (data.measures) {
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

        // ------------------------------------------------------------------

        /**
         * Widget for the displaying exceptions
         *
         * Options:
         *  - data
         */
        var ExceptionsWidget = PhpDebugBar.Widgets.ExceptionsWidget = PhpDebugBar.Widget.extend({

            className: csscls('exceptions'),

            render: function() {
                this.$list = new ListWidget({ itemRenderer: function(li, e) {
                    $('<span />').addClass(csscls('message')).text(e.message).appendTo(li);
                    if (e.file) {
                        $('<span />').addClass(csscls('filename')).text(e.file + "#" + e.line).appendTo(li);
                    }
                    if (e.type) {
                        $('<span />').addClass(csscls('type')).text(e.type).appendTo(li);
                    }
                    if (e.surrounding_lines) {
                        var pre = createCodeBlock(e.surrounding_lines.join(""), 'php').addClass(csscls('file')).appendTo(li);
                        li.click(function() {
                            if (pre.is(':visible')) {
                                pre.hide();
                            } else {
                                pre.show();
                            }
                        });
                    }
                }});
                this.$list.$el.appendTo(this.$el);

                this.bindAttr('data', function(data) {
                    this.$list.set('data', data);
                    if (data.length == 1) {
                        this.$list.$el.children().first().find(csscls('.file')).show();
                    }
                });

            }

        });


    })(PhpDebugBar.$);

    if (typeof(PhpDebugBar) == 'undefined') {
        // namespace
        var PhpDebugBar = {};
        PhpDebugBar.$ = jQuery;
    }

    (function($) {

        var csscls = function(cls) {
            return PhpDebugBar.utils.csscls(cls, 'phpdebugbar-openhandler-');
        };

        PhpDebugBar.OpenHandler = PhpDebugBar.Widget.extend({

            className: 'phpdebugbar-openhandler',

            defaults: {
                items_per_page: 20
            },

            render: function() {
                var self = this;

                this.$el.appendTo('body').hide();
                this.$closebtn = $('<a><i class="fa fa-times"></i></a>');
                this.$table = $('<tbody />');
                $('<div>PHP DebugBar | Open</div>').addClass(csscls('header')).append(this.$closebtn).appendTo(this.$el);
                $('<table><thead><tr><th width="150">Date</th><th width="55">Method</th><th>URL</th><th width="125">IP</th><th width="100">Filter data</th></tr></thead></table>').append(this.$table).appendTo(this.$el);
                this.$actions = $('<div />').addClass(csscls('actions')).appendTo(this.$el);

                this.$closebtn.on('click', function() {
                    self.hide();
                });

                this.$loadmorebtn = $('<a>Load more</a>')
                    .appendTo(this.$actions)
                    .on('click', function() {
                        self.find(self.last_find_request, self.last_find_request.offset + self.get('items_per_page'), self.handleFind.bind(self));
                    });

                this.$showonlycurrentbtn = $('<a>Show only current URL</a>')
                    .appendTo(this.$actions)
                    .on('click', function() {
                        self.$table.empty();
                        self.find({uri: window.location.pathname}, 0, self.handleFind.bind(self));
                    });

                this.$showallbtn = $('<a>Show all</a>')
                    .appendTo(this.$actions)
                    .on('click', function() {
                        self.refresh();
                    });

                this.$clearbtn = $('<a>Delete all</a>')
                    .appendTo(this.$actions)
                    .on('click', function() {
                        self.clear(function() {
                            self.hide();
                        });
                    });

                this.addSearch();

                this.$overlay = $('<div />').addClass(csscls('overlay')).hide().appendTo('body');
                this.$overlay.on('click', function() {
                    self.hide();
                });
            },

            refresh: function() {
                this.$table.empty();
                this.$loadmorebtn.show();
                this.find({}, 0, this.handleFind.bind(this));
            },

            addSearch: function(){
                var self = this;
                var searchBtn = $('<button />')
                    .text('Search')
                    .attr('type', 'submit')
                    .on('click', function(e) {
                        self.$table.empty();
                        var search = {};
                        var a = $(this).parent().serializeArray();
                        $.each(a, function() {
                            if(this.value){
                                search[this.name] = this.value;
                            }
                        });

                        self.find(search, 0, self.handleFind.bind(self));
                        e.preventDefault();
                    });

                $('<form />')
                    .append('<br/><b>Filter results</b><br/>')
                    .append('Method: <select name="method"><option></option><option>GET</option><option>POST</option><option>PUT</option><option>DELETE</option></select><br/>')
                    .append('Uri: <input type="text" name="uri"><br/>')
                    .append('IP: <input type="text" name="ip"><br/>')
                    .append(searchBtn)
                    .appendTo(this.$actions);
            },

            handleFind: function(data) {
                var self = this;
                $.each(data, function(i, meta) {
                    var a = $('<a />')
                        .text('Load dataset')
                        .on('click', function(e) {
                            self.hide();
                            self.load(meta['id'], function(data) {
                                self.callback(meta['id'], data);
                            });
                            e.preventDefault();
                        });

                    var method = $('<a />')
                        .text(meta['method'])
                        .on('click', function(e) {
                            self.$table.empty();
                            self.find({method: meta['method']}, 0, self.handleFind.bind(self));
                            e.preventDefault();
                        });

                    var uri = $('<a />')
                        .text(meta['uri'])
                        .on('click', function(e) {
                            self.hide();
                            self.load(meta['id'], function(data) {
                                self.callback(meta['id'], data);
                            });
                            e.preventDefault();
                        });

                    var ip = $('<a />')
                        .text(meta['ip'])
                        .on('click', function(e) {
                            self.$table.empty();
                            self.find({ip: meta['ip']}, 0, self.handleFind.bind(self));
                            e.preventDefault();
                        });

                    var search = $('<a />')
                        .text('Show URL')
                        .on('click', function(e) {
                            self.$table.empty();
                            self.find({uri: meta['uri']}, 0, self.handleFind.bind(self));
                            e.preventDefault();
                        });

                    $('<tr />')
                        .append('<td>' + meta['datetime'] + '</td>')
                        .append('<td>' + meta['method'] + '</td>')
                        .append($('<td />').append(uri))
                        .append($('<td />').append(ip))
                        .append($('<td />').append(search))
                        .appendTo(self.$table);
                });
                if (data.length < this.get('items_per_page')) {
                    this.$loadmorebtn.hide();
                }
            },

            show: function(callback) {
                this.callback = callback;
                this.$el.show();
                this.$overlay.show();
                this.refresh();
            },

            hide: function() {
                this.$el.hide();
                this.$overlay.hide();
            },

            find: function(filters, offset, callback) {
                var data = $.extend({}, filters, {max: this.get('items_per_page'), offset: offset || 0});
                this.last_find_request = data;
                this.ajax(data, callback);
            },

            load: function(id, callback) {
                this.ajax({op: "get", id: id}, callback);
            },

            clear: function(callback) {
                this.ajax({op: "clear"}, callback);
            },

            ajax: function(data, callback) {
                $.ajax({
                    dataType: 'json',
                    url: this.get('url'),
                    data: data,
                    success: callback,
                    ignoreDebugBarAjaxHandler: true
                });
            }

        });

    })(PhpDebugBar.$);

    (function($) {

        var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

        /**
         * Widget for the displaying templates data
         *
         * Options:
         *  - data
         */
        var TemplatesWidget = PhpDebugBar.Widgets.TemplatesWidget = PhpDebugBar.Widget.extend({

            className: csscls('templates'),

            render: function() {
                this.$status = $('<div />').addClass(csscls('status')).appendTo(this.$el);

                this.$list = new  PhpDebugBar.Widgets.ListWidget({ itemRenderer: function(li, tpl) {
                    $('<span />').addClass(csscls('name')).text(tpl.name).appendTo(li);
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

                this.bindAttr('data', function(data) {
                    this.$list.set('data', data.templates);
                    this.$status.empty();

                    var sentence = data.sentence || "templates were rendered";
                    $('<span />').text(data.templates.length + " " + sentence).appendTo(this.$status);

                    if (data.accumulated_render_time_str) {
                        this.$status.append($('<span title="Accumulated render time" />').addClass(csscls('render-time')).text(data.accumulated_render_time_str));
                    }
                    if (data.memory_usage_str) {
                        this.$status.append($('<span title="Memory usage" />').addClass(csscls('memory')).text(data.memory_usage_str));
                    }
                });
            }

        });

    })(PhpDebugBar.$);

    (function($) {

        var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

        /**
         * Widget for the displaying sql queries
         *
         * Options:
         *  - data
         */
        var SQLQueriesWidget = PhpDebugBar.Widgets.SQLQueriesWidget = PhpDebugBar.Widget.extend({

            className: csscls('sqlqueries'),

            onFilterClick: function(el) {
                $(el).toggleClass(csscls('excluded'));

                var excludedLabels = [];
                this.$toolbar.find(csscls('.filter') + csscls('.excluded')).each(function() {
                    excludedLabels.push(this.rel);
                });

                this.$list.$el.find("li[connection=" + $(el).attr("rel") + "]").toggle();

                this.set('exclude', excludedLabels);
            },

            render: function() {
                this.$status = $('<div />').addClass(csscls('status')).appendTo(this.$el);

                this.$toolbar = $('<div></div>').addClass(csscls('toolbar')).appendTo(this.$el);

                var filters = [], self = this;

                this.$list = new PhpDebugBar.Widgets.ListWidget({ itemRenderer: function(li, stmt) {
                    $('<code />').addClass(csscls('sql')).html(PhpDebugBar.Widgets.highlight(stmt.sql, 'sql')).appendTo(li);
                    if (stmt.duration_str) {
                        $('<span title="Duration" />').addClass(csscls('duration')).text(stmt.duration_str).appendTo(li);
                    }
                    if (stmt.memory_str) {
                        $('<span title="Memory usage" />').addClass(csscls('memory')).text(stmt.memory_str).appendTo(li);
                    }
                    if (typeof(stmt.is_success) != 'undefined' && !stmt.is_success) {
                        li.addClass(csscls('error'));
                        li.append($('<span />').addClass(csscls('error')).text("[" + stmt.error_code + "] " + stmt.error_message));
                    } else if (typeof(stmt.row_count) != 'undefined') {
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
                                .on('click', function() { self.onFilterClick(this); })
                                .appendTo(self.$toolbar);
                            if (filters.length>1) {
                                self.$toolbar.show();
                                self.$list.$el.css("margin-bottom","20px");
                            }
                        }
                    }
                    if (stmt.params && !$.isEmptyObject(stmt.params)) {
                        var table = $('<table><tr><th colspan="2">Params</th></tr></table>').addClass(csscls('params')).appendTo(li);
                        for (var key in stmt.params) {
                            if (typeof stmt.params[key] !== 'function') {
                                table.append('<tr><td class="' + csscls('name') + '">' + key + '</td><td class="' + csscls('value') +
                                    '">' + stmt.params[key] + '</td></tr>');
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

                this.bindAttr('data', function(data) {
                    this.$list.set('data', data.statements);
                    this.$status.empty();

                    // Search for duplicate statements.
                    for (var sql = {}, duplicate = 0, i = 0; i < data.statements.length; i++) {
                        var stmt = data.statements[i].sql;
                        if (data.statements[i].params && !$.isEmptyObject(data.statements[i].params)) {
                            stmt += ' {' + $.param(data.statements[i].params, false) + '}';
                        }
                        sql[stmt] = sql[stmt] || { keys: [] };
                        sql[stmt].keys.push(i);
                    }
                    // Add classes to all duplicate SQL statements.
                    for (var stmt in sql) {
                        if (sql[stmt].keys.length > 1) {
                            duplicate++;
                            for (var i = 0; i < sql[stmt].keys.length; i++) {
                                this.$list.$el.find('.' + csscls('list-item')).eq(sql[stmt].keys[i])
                                    .addClass(csscls('sql-duplicate')).addClass(csscls('sql-duplicate-'+duplicate));
                            }
                        }
                    }

                    var t = $('<span />').text(data.nb_statements + " statements were executed").appendTo(this.$status);
                    if (data.nb_failed_statements) {
                        t.append(", " + data.nb_failed_statements + " of which failed");
                    }
                    if (duplicate) {
                        t.append(", " + duplicate + " of which were duplicated");
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

    (function($) {

        var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

        /**
         * Widget for the displaying mails data
         *
         * Options:
         *  - data
         */
        var MailsWidget = PhpDebugBar.Widgets.MailsWidget = PhpDebugBar.Widget.extend({

            className: csscls('mails'),

            render: function() {
                this.$list = new  PhpDebugBar.Widgets.ListWidget({ itemRenderer: function(li, mail) {
                    $('<span />').addClass(csscls('subject')).text(mail.subject).appendTo(li);
                    $('<span />').addClass(csscls('to')).text(mail.to).appendTo(li);
                    if (mail.headers) {
                        var headers = $('<pre />').addClass(csscls('headers')).appendTo(li);
                        $('<code />').text(mail.headers).appendTo(headers);
                        li.click(function() {
                            if (headers.is(':visible')) {
                                headers.hide();
                            } else {
                                headers.show();
                            }
                        });
                    }
                }});
                this.$list.$el.appendTo(this.$el);

                this.bindAttr('data', function(data) {
                    this.$list.set('data', data);
                });
            }

        });

    })(PhpDebugBar.$);


    return PhpDebugBar;
});