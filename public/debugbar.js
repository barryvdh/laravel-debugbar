if (typeof(PhpDebugBar) == 'undefined') {
    // namespace
    var PhpDebugBar = {};
}

if (typeof(localStorage) == 'undefined') {
    // provide mock localStorage object for dumb browsers
    localStorage = {
        setItem: function(key, value) {},
        getItem: function(key) { return null; }
    };
}

(function($) {

    /**
     * Returns the value from an object property.
     * Using dots in the key, it is possible to retrieve nested property values
     * 
     * @param {Object} dict
     * @param {String} key
     * @param {Object} default_value
     * @return {Object}
     */
    function getDictValue(dict, key, default_value) {
        var d = dict, parts = key.split('.');
        for (var i = 0; i < parts.length; i++) {
            if (!d[parts[i]]) {
                return default_value;
            }
            d = d[parts[i]];
        }
        return d;
    }

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

        className: 'phpdebugbar-panel',

        render: function() {
            this.$tab = $('<a href="javascript:" class="phpdebugbar-tab" />');
            this.bindAttr('title', $('<span class="text" />').appendTo(this.$tab));

            this.$badge = $('<span class="badge" />').appendTo(this.$tab);
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
     *  - position: "right" or "left"
     *  - data: alias of title
     */
    var Indicator = Widget.extend({

        tagName: 'span',

        className: 'phpdebugbar-indicator',

        defaults: {
            position: "right"
        },

        render: function() {
            this.bindAttr('position', function(pos) { this.$el.css('float', pos); });

            this.$icon = $('<i />').appendTo(this.$el);
            this.bindAttr('icon', function(icon) {
                if (icon) {
                    this.$icon.attr('class', 'icon-' + icon);
                } else {
                    this.$icon.attr('class', '');
                }
            });

            this.bindAttr(['title', 'data'], $('<span class="text" />').appendTo(this.$el));

            this.$tooltip = $('<span class="tooltip disabled" />').appendTo(this.$el);
            this.bindAttr('tooltip', function(tooltip) {
                if (tooltip) {
                    this.$tooltip.text(tooltip).removeClass('disabled');
                } else {
                    this.$tooltip.addClass('disabled');
                }
            });
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

        className: "phpdebugbar",

        options: {
            bodyPaddingBottom: true
        },

        initialize: function() {
            this.controls = {};
            this.dataMap = {};
            this.datasets = {};
            this.firstTabName = null;
            this.activePanelName = null;
        },

        /**
         * Initialiazes the UI
         *
         * @this {DebugBar}
         */
        render: function() {
            var self = this;
            this.$el.appendTo('body');
            this.$header = $('<div class="phpdebugbar-header" />').appendTo(this.$el);
            var $body = this.$body = $('<div class="phpdebugbar-body" />').appendTo(this.$el);
            this.$resizehdle = $('<div class="phpdebugbar-resize-handle" />').appendTo(this.$body);
            this.recomputeBottomOffset();

            // dragging of resize handle
            var dragging = false;
            this.$resizehdle.on('mousedown', function(e) {
                var orig_h = $body.height(), pos_y = e.pageY;
                dragging = true;

                $body.parents().on('mousemove', function(e) {
                    if (dragging) {
                        var h = orig_h + (pos_y - e.pageY);
                        $body.css('height', h);
                        localStorage.setItem('phpdebugbar-height', h);
                        self.recomputeBottomOffset();
                    }
                }).on('mouseup', function() {
                    dragging = false;
                });

                e.preventDefault();
            });
            
            // minimize button
            this.$minimizebtn = $('<a class="phpdebugbar-minimize-btn" href="javascript:"><i class="icon-remove"></i></a>').appendTo(this.$header);
            this.$minimizebtn.click(function() {
                self.minimize();
            });

            // open button
            this.$openbtn = $('<a class="phpdebugbar-open-btn" href="javascript:"><i class="icon-folder-open"></i></a>').appendTo(this.$header).hide();
            this.$openbtn.click(function() {
                self.openHandler.show(function(id, dataset) {
                    self.addDataSet(dataset, id, id + " (opened)");
                });
            });

            // select box for data sets
            this.$datasets = $('<select class="phpdebugbar-datasets-switcher" />').appendTo(this.$header);
            this.$datasets.change(function() {
                self.dataChangeHandler(self.datasets[this.value]);
            });
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
            if (height) {
                this.$body.css('height', height);
            } else {
                localStorage.setItem('phpdebugbar-height', this.$body.height());
            }

            // bar visibility
            var visible = localStorage.getItem('phpdebugbar-visible');
            if (visible && visible == '1') {
                var tab = localStorage.getItem('phpdebugbar-tab');
                if (this.isTab(tab)) {
                    this.showTab(tab);
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
            tab.$tab.appendTo(this.$header).click(function() { self.showTab(name); });
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
                tooltip: tooltip, 
                position: position || 'right'
            });
            return this.addIndicator(name, indicator);
        },

        /**
         * Adds an indicator
         * 
         * @this {DebugBar}
         * @param {String} name Internal name
         * @param {Indicator} indicator Indicator object
         * @return {Indicator}
         */
        addIndicator: function(name, indicator) {
            if (this.isControl(name)) {
                throw new Error(name + ' already exists');
            }

            if (indicator.get('position') == 'right') {
                indicator.$el.appendTo(this.$header);
            } else {
                indicator.$el.insertBefore(this.$header.children().first())
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
            this.$minimizebtn.show();
            this.recomputeBottomOffset();

            $(this.$header).find('> .active').removeClass('active');
            $(this.$body).find('> .active').removeClass('active');

            this.controls[name].$tab.addClass('active');
            this.controls[name].$el.addClass('active');
            this.activePanelName = name;

            localStorage.setItem('phpdebugbar-visible', '1');
            localStorage.setItem('phpdebugbar-tab', name);
        },

        /**
         * Hide panels and "close" the debug bar
         *
         * @this {DebugBar}
         */
        minimize: function() {
            this.$header.find('> .active').removeClass('active');
            this.$body.hide();
            this.$minimizebtn.hide();
            this.$resizehdle.hide();
            this.recomputeBottomOffset();
            localStorage.setItem('phpdebugbar-visible', '0');
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
         * @param {String} label
         * @return {String} Dataset's id
         */
        addDataSet: function(data, id, label) {
            id = id || ("Request #" + (Object.keys(this.datasets).length + 1));
            label = label || id;
            this.datasets[id] = data;

            this.$datasets.append($('<option value="' + id + '">' + label + '</option>'));
            if (Object.keys(this.datasets).length > 1) {
                this.$datasets.show();
            }

            this.showDataSet(id);
            return id;
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

})(jQuery);
