(function() {
    'use strict';

    const STORAGE_KEY = 'phpdebugbar_floating_position';
    const SETTINGS_KEY = 'phpdebugbar-settings';
    const SNAP_CHECK_THRESHOLD = 10;
    const MUTATION_OBSERVER_MAX_COUNT = 100;

    function getEventCoordinates(e) {
        const touch = e.touches?.[0] || e.changedTouches?.[0];
        return touch
            ? { x: touch.clientX, y: touch.clientY }
            : { x: e.clientX, y: e.clientY };
    }

    (function preventFOUC() {
        try {
            // Check if FOUC fix already exists (may be injected inline by PHP)
            if (document.getElementById('phpdebugbar-fouc-fix')) return;

            const settings = JSON.parse(localStorage.getItem(SETTINGS_KEY) || '{}');
            const config = window.phpdebugbar_position_config || {};
            const mode = settings.positionMode || config.position;

            if (mode === 'floating') {
                const style = document.createElement('style');
                style.id = 'phpdebugbar-fouc-fix';
                style.textContent = 'div.phpdebugbar:not(.phpdebugbar-ready){opacity:0!important;transition:opacity .15s ease!important}';
                (document.head || document.documentElement).appendChild(style);
            }
        } catch (e) {}
    })();

    const SNAP_ZONES = {
        topLeft: {
            name: 'Top Left',
            detect: (x, y, w, h, vw, vh, dist) => x <= dist && y <= dist,
            getPosition: (w, h, vw, vh) => ({ x: 0, y: 0, width: vw / 2, height: vh / 2 }),
            priority: 0
        },
        topRight: {
            name: 'Top Right',
            detect: (x, y, w, h, vw, vh, dist) => vw - (x + w) <= dist && y <= dist,
            getPosition: (w, h, vw, vh) => ({ x: vw / 2, y: 0, width: vw / 2, height: vh / 2 }),
            priority: 0
        },
        bottomLeft: {
            name: 'Bottom Left',
            detect: (x, y, w, h, vw, vh, dist) => x <= dist && vh - (y + h) <= dist,
            getPosition: (w, h, vw, vh) => ({ x: 0, y: vh / 2, width: vw / 2, height: vh / 2 }),
            priority: 0
        },
        bottomRight: {
            name: 'Bottom Right',
            detect: (x, y, w, h, vw, vh, dist) => vw - (x + w) <= dist && vh - (y + h) <= dist,
            getPosition: (w, h, vw, vh) => ({ x: vw / 2, y: vh / 2, width: vw / 2, height: vh / 2 }),
            priority: 0
        },
        bottom: {
            name: 'Bottom',
            detect: (x, y, w, h, vw, vh, dist) => vh - (y + h) <= dist,
            getPosition: (w, h, vw, vh) => ({ x: 0, y: vh - h, width: vw, height: h }),
            priority: 1
        },
        top: {
            name: 'Top',
            detect: (x, y, w, h, vw, vh, dist) => y <= dist,
            getPosition: (w, h, vw, vh) => ({ x: 0, y: 0, width: vw, height: h }),
            priority: 1
        },
        left: {
            name: 'Left Half',
            detect: (x, y, w, h, vw, vh, dist) => x <= dist,
            getPosition: (w, h, vw, vh) => ({ x: 0, y: 0, width: vw / 2, height: vh }),
            priority: 2
        },
        right: {
            name: 'Right Half',
            detect: (x, y, w, h, vw, vh, dist) => vw - (x + w) <= dist,
            getPosition: (w, h, vw, vh) => ({ x: vw / 2, y: 0, width: vw / 2, height: vh }),
            priority: 2
        }
    };

    const SORTED_ZONE_ENTRIES = Object.entries(SNAP_ZONES)
        .sort((a, b) => a[1].priority - b[1].priority);

    class SnapPreview {
        constructor() {
            this.element = null;
            this.currentZone = null;
        }

        create() {
            if (this.element) return;
            this.element = document.createElement('div');
            this.element.className = 'phpdebugbar-snap-preview';
            document.body.appendChild(this.element);
        }

        show(position, zoneName) {
            this.create();
            this.element.style.cssText = `
                left: ${position.x}px;
                top: ${position.y}px;
                width: ${position.width}px;
                height: ${position.height}px;
            `;
            this.element.classList.add('phpdebugbar-snap-preview-active');
            this.currentZone = zoneName;
        }

        hide() {
            if (this.element) {
                this.element.classList.remove('phpdebugbar-snap-preview-active');
            }
            this.currentZone = null;
        }

        destroy() {
            if (this.element && this.element.parentNode) {
                this.element.parentNode.removeChild(this.element);
                this.element = null;
            }
        }
    }

    class DraggableDebugbar {
        constructor(debugbar, options = {}) {
            this.debugbar = debugbar;
            this.options = Object.assign({
                initial_x: null,
                initial_y: null,
                remember_position: true,
                storage_key: STORAGE_KEY,
                enableSnapping: true,
                snapDistance: 60,
                snapAnimationDuration: 200
            }, options);

            this.isDragging = false;
            this.startX = 0;
            this.startY = 0;
            this.currentX = 0;
            this.currentY = 0;
            this.currentSnapZone = null;
            this.isSnapped = false;
            this.rafId = null;
            this.pendingX = 0;
            this.pendingY = 0;
            this.cachedWidth = 0;
            this.cachedHeight = 0;
            this.cachedViewportWidth = 0;
            this.cachedViewportHeight = 0;
            this.lastSnapCheckX = 0;
            this.lastSnapCheckY = 0;

            this.snapPreview = new SnapPreview();
            this.boundStartDrag = this.startDrag.bind(this);
            this.boundDrag = this.drag.bind(this);
            this.boundEndDrag = this.endDrag.bind(this);
            this.boundResize = this.throttle(this.handleResize.bind(this), 100);

            this.init();
        }

        throttle(func, limit) {
            let inThrottle;
            return (...args) => {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        }

        init() {
            this.dragHandle = this.debugbar.querySelector('.phpdebugbar-header');
            if (!this.dragHandle) {
                this.reveal();
                return;
            }

            this.debugbar.classList.add('phpdebugbar-floating');
            this.dragHandle.classList.add('phpdebugbar-drag-handle');
            this.dragHandle.addEventListener('mousedown', this.boundStartDrag);
            this.dragHandle.addEventListener('touchstart', this.boundStartDrag, { passive: false });
            window.addEventListener('resize', this.boundResize, { passive: true });

            this.loadPosition();
            this.reveal();
        }

        reveal() {
            this.debugbar.classList.add('phpdebugbar-ready');
            const foucStyle = document.getElementById('phpdebugbar-fouc-fix');
            if (foucStyle) foucStyle.remove();
        }

        detectSnapZoneCached(x, y) {
            if (!this.options.enableSnapping) return null;

            const w = this.cachedWidth;
            const h = this.cachedHeight;
            const vw = this.cachedViewportWidth;
            const vh = this.cachedViewportHeight;
            const dist = this.options.snapDistance;

            for (const [key, zone] of SORTED_ZONE_ENTRIES) {
                if (zone.detect(x, y, w, h, vw, vh, dist)) {
                    return key;
                }
            }
            return null;
        }

        startDrag(e) {
            const target = e.target;
            if (target.closest('.phpdebugbar-tab') ||
                target.closest('.phpdebugbar-close-btn') ||
                target.closest('.phpdebugbar-minimize-btn') ||
                target.closest('.phpdebugbar-restore-btn') ||
                target.closest('.phpdebugbar-open-btn') ||
                target.closest('.phpdebugbar-tab-settings') ||
                target.closest('.phpdebugbar-tab-history') ||
                target.closest('a') ||
                target.closest('button') ||
                target.closest('select') ||
                target.closest('input')) {
                return;
            }

            e.preventDefault();
            this.isDragging = true;
            this.debugbar.classList.add('phpdebugbar-dragging');

            if (this.isSnapped) {
                this.unsnap();
            }

            const rect = this.debugbar.getBoundingClientRect();
            this.cachedWidth = rect.width;
            this.cachedHeight = rect.height;
            this.cachedViewportWidth = window.innerWidth;
            this.cachedViewportHeight = window.innerHeight;

            const coords = getEventCoordinates(e);
            this.startX = coords.x - rect.left;
            this.startY = coords.y - rect.top;
            this.currentX = rect.left;
            this.currentY = rect.top;
            this.lastSnapCheckX = rect.left;
            this.lastSnapCheckY = rect.top;

            this.debugbar.style.left = '0';
            this.debugbar.style.top = '0';
            this.debugbar.style.transform = `translate3d(${this.currentX}px, ${this.currentY}px, 0)`;

            document.addEventListener('mousemove', this.boundDrag);
            document.addEventListener('mouseup', this.boundEndDrag);
            document.addEventListener('touchmove', this.boundDrag, { passive: false });
            document.addEventListener('touchend', this.boundEndDrag);
            document.addEventListener('touchcancel', this.boundEndDrag);
        }

        drag(e) {
            if (!this.isDragging) return;
            e.preventDefault();

            const coords = getEventCoordinates(e);
            let newX = coords.x - this.startX;
            let newY = coords.y - this.startY;

            const minVisible = 50;
            const maxX = this.cachedViewportWidth - minVisible;
            const minX = minVisible - this.cachedWidth;
            newX = Math.max(minX, Math.min(newX, maxX));

            const maxY = this.cachedViewportHeight - minVisible;
            newY = Math.max(0, Math.min(newY, maxY));

            this.pendingX = newX;
            this.pendingY = newY;

            if (!this.rafId) {
                this.rafId = requestAnimationFrame(() => {
                    this.rafId = null;
                    this.applyPositionDuringDrag(this.pendingX, this.pendingY);

                    const dx = Math.abs(this.pendingX - this.lastSnapCheckX);
                    const dy = Math.abs(this.pendingY - this.lastSnapCheckY);
                    if (dx > SNAP_CHECK_THRESHOLD || dy > SNAP_CHECK_THRESHOLD) {
                        this.updateSnapPreview(this.pendingX, this.pendingY);
                        this.lastSnapCheckX = this.pendingX;
                        this.lastSnapCheckY = this.pendingY;
                    }
                });
            }
        }

        applyPositionDuringDrag(x, y) {
            this.debugbar.style.transform = `translate3d(${x}px, ${y}px, 0)`;
            this.currentX = x;
            this.currentY = y;
        }

        updateSnapPreview(x, y) {
            const snapZone = this.detectSnapZoneCached(x, y);

            if (snapZone) {
                const zone = SNAP_ZONES[snapZone];
                const snapPos = zone.getPosition(
                    this.cachedWidth,
                    this.cachedHeight,
                    this.cachedViewportWidth,
                    this.cachedViewportHeight
                );
                this.snapPreview.show(snapPos, snapZone);
                this.currentSnapZone = snapZone;
            } else {
                this.snapPreview.hide();
                this.currentSnapZone = null;
            }
        }

        endDrag() {
            if (!this.isDragging) return;

            if (this.rafId) {
                cancelAnimationFrame(this.rafId);
                this.rafId = null;
            }

            this.isDragging = false;
            this.debugbar.classList.remove('phpdebugbar-dragging');

            document.removeEventListener('mousemove', this.boundDrag);
            document.removeEventListener('mouseup', this.boundEndDrag);
            document.removeEventListener('touchmove', this.boundDrag);
            document.removeEventListener('touchend', this.boundEndDrag);
            document.removeEventListener('touchcancel', this.boundEndDrag);

            this.snapPreview.hide();
            this.debugbar.style.transform = '';
            this.applyPosition(this.currentX, this.currentY);

            if (this.currentSnapZone && this.options.enableSnapping) {
                this.snapTo(this.currentSnapZone);
            } else if (this.options.remember_position) {
                this.savePosition();
            }

            this.currentSnapZone = null;
        }

        snapTo(zoneName) {
            const zone = SNAP_ZONES[zoneName];
            if (!zone) return;

            const rect = this.debugbar.getBoundingClientRect();
            const vw = window.innerWidth;
            const vh = window.innerHeight;
            const targetPos = zone.getPosition(rect.width, rect.height, vw, vh);

            this.debugbar.classList.add('phpdebugbar-snapping');

            if (zoneName === 'bottom' || zoneName === 'top') {
                this.debugbar.style.width = '100%';
                this.debugbar.style.maxWidth = '100vw';
                this.debugbar.style.borderRadius = '0';
            }

            this.debugbar.style.transition = `left ${this.options.snapAnimationDuration}ms cubic-bezier(0.25, 0.46, 0.45, 0.94),
                                              top ${this.options.snapAnimationDuration}ms cubic-bezier(0.25, 0.46, 0.45, 0.94),
                                              width ${this.options.snapAnimationDuration}ms cubic-bezier(0.25, 0.46, 0.45, 0.94)`;

            this.applyPosition(targetPos.x, targetPos.y);

            let cleaned = false;
            const cleanup = () => {
                if (cleaned) return;
                cleaned = true;
                this.debugbar.style.transition = '';
                this.debugbar.classList.remove('phpdebugbar-snapping');
                this.isSnapped = true;
                this.currentSnapZone = zoneName;
                this.debugbar.removeEventListener('transitionend', onTransitionEnd);

                if (this.options.remember_position) {
                    this.savePosition();
                }
            };

            const onTransitionEnd = (e) => {
                if (e.target === this.debugbar && e.propertyName === 'left') {
                    cleanup();
                }
            };

            this.debugbar.addEventListener('transitionend', onTransitionEnd);
            setTimeout(cleanup, this.options.snapAnimationDuration + 50);
        }

        unsnap() {
            if (!this.isSnapped) return;
            this.debugbar.style.width = '';
            this.debugbar.style.maxWidth = '';
            this.debugbar.style.borderRadius = '';
            this.isSnapped = false;
            this.currentSnapZone = null;
        }

        getConstrainedPosition(x, y) {
            const rect = this.debugbar.getBoundingClientRect();
            const vw = window.innerWidth;
            const vh = window.innerHeight;
            const minVisible = 50;

            return {
                x: Math.max(minVisible - rect.width, Math.min(x, vw - minVisible)),
                y: Math.max(0, Math.min(y, vh - minVisible))
            };
        }

        handleResize() {
            if (!this.debugbar.classList.contains('phpdebugbar-floating')) return;

            if (this.isSnapped && this.currentSnapZone) {
                const zone = SNAP_ZONES[this.currentSnapZone];
                if (zone) {
                    const rect = this.debugbar.getBoundingClientRect();
                    const targetPos = zone.getPosition(rect.width, rect.height, window.innerWidth, window.innerHeight);
                    this.applyPosition(targetPos.x, targetPos.y);
                }
            } else {
                const rect = this.debugbar.getBoundingClientRect();
                const constrained = this.getConstrainedPosition(rect.left, rect.top);
                if (rect.left !== constrained.x || rect.top !== constrained.y) {
                    this.applyPosition(constrained.x, constrained.y);
                    if (this.options.remember_position) {
                        this.savePosition();
                    }
                }
            }
        }

        applyPosition(x, y) {
            this.debugbar.style.left = x + 'px';
            this.debugbar.style.top = y + 'px';
            this.debugbar.style.right = 'auto';
            this.debugbar.style.bottom = 'auto';
            this.currentX = x;
            this.currentY = y;
        }

        savePosition() {
            try {
                localStorage.setItem(this.options.storage_key, JSON.stringify({
                    x: this.currentX,
                    y: this.currentY,
                    snapped: this.isSnapped,
                    snapZone: this.currentSnapZone,
                    timestamp: Date.now()
                }));
            } catch (e) {}
        }

        loadPosition() {
            let loaded = false;

            if (this.options.remember_position) {
                try {
                    const saved = localStorage.getItem(this.options.storage_key);
                    if (saved) {
                        const position = JSON.parse(saved);
                        if (position.snapped && position.snapZone && SNAP_ZONES[position.snapZone]) {
                            this.snapTo(position.snapZone);
                            loaded = true;
                        } else if (position.x !== undefined && position.y !== undefined) {
                            const constrained = this.getConstrainedPosition(position.x, position.y);
                            this.applyPosition(constrained.x, constrained.y);
                            loaded = true;
                        }
                    }
                } catch (e) {}
            }

            if (!loaded) {
                this.setInitialPosition();
            }
        }

        setInitialPosition() {
            const vw = window.innerWidth;
            const vh = window.innerHeight;
            const rect = this.debugbar.getBoundingClientRect();

            let x = this.options.initial_x;
            let y = this.options.initial_y;

            if (x === null || x === undefined) {
                x = vw - rect.width - 10;
            }
            if (y === null || y === undefined) {
                y = vh - rect.height - 10;
            }

            const constrained = this.getConstrainedPosition(x, y);
            this.applyPosition(constrained.x, constrained.y);
        }

        resetPosition() {
            try {
                localStorage.removeItem(this.options.storage_key);
            } catch (e) {}
            this.unsnap();
            this.setInitialPosition();
        }

        destroy() {
            this.snapPreview.destroy();
            window.removeEventListener('resize', this.boundResize);
            if (this.dragHandle) {
                this.dragHandle.removeEventListener('mousedown', this.boundStartDrag);
                this.dragHandle.removeEventListener('touchstart', this.boundStartDrag);
            }
            if (this.rafId) {
                cancelAnimationFrame(this.rafId);
            }
        }
    }

    let cachedSettings = null;

    function getSettings() {
        if (!cachedSettings) {
            try {
                cachedSettings = JSON.parse(localStorage.getItem(SETTINGS_KEY) || '{}');
            } catch (e) {
                cachedSettings = {};
            }
        }
        return cachedSettings;
    }

    function updateSettings(key, value) {
        const settings = getSettings();
        settings[key] = value;
        cachedSettings = settings;
        try {
            localStorage.setItem(SETTINGS_KEY, JSON.stringify(settings));
        } catch (e) {}
    }

    function initDraggableDebugbar(retries = 0) {
        const config = window.phpdebugbar_position_config || {};

        if (config.position !== 'floating') {
            return;
        }

        const debugbar = document.querySelector('div.phpdebugbar');
        if (!debugbar) {
            if (retries < 50) {
                setTimeout(() => initDraggableDebugbar(retries + 1), 100);
            }
            return;
        }

        if (debugbar.classList.contains('phpdebugbar-floating')) {
            return;
        }

        window.phpdebugbar_draggable = new DraggableDebugbar(debugbar, config.floating || {});
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => initDraggableDebugbar());
    } else {
        setTimeout(() => initDraggableDebugbar(), 0);
    }

    window.DraggableDebugbar = DraggableDebugbar;

    function hookSettingsWidget(retries = 0) {
        if (typeof window.phpdebugbar === 'undefined' || !window.phpdebugbar.settings) {
            if (retries < 100) {
                setTimeout(() => hookSettingsWidget(retries + 1), 50);
            }
            return;
        }

        if (typeof PhpDebugBar === 'undefined' || !PhpDebugBar.$) {
            if (retries < 100) {
                setTimeout(() => hookSettingsWidget(retries + 1), 50);
            }
            return;
        }

        const $ = PhpDebugBar.$;
        const settingsWidget = window.phpdebugbar.settings.get('widget');

        if (!settingsWidget) {
            if (retries < 100) {
                setTimeout(() => hookSettingsWidget(retries + 1), 50);
            }
            return;
        }

        if (settingsWidget._positionModeHooked) {
            return;
        }
        settingsWidget._positionModeHooked = true;

        const originalRender = settingsWidget.render;

        settingsWidget.render = function() {
            originalRender.call(this);
            addPositionModeToSettings.call(this, $);
        };

        if (settingsWidget.$el && settingsWidget.$el.length > 0) {
            const $existingForm = settingsWidget.$el;
            if ($existingForm.find('.phpdebugbar-form-row').length > 0 &&
                $existingForm.find('[data-position-mode-field]').length === 0) {
                addPositionModeToSettings.call(settingsWidget, $);
            }
        }

        const debugbarEl = document.querySelector('div.phpdebugbar');
        if (debugbarEl) {
            let observationCount = 0;
            const observer = new MutationObserver((_, obs) => {
                observationCount++;
                if (observationCount > MUTATION_OBSERVER_MAX_COUNT) {
                    obs.disconnect();
                    return;
                }
                const $form = $('form.phpdebugbar-settings');
                if ($form.length > 0 &&
                    $form.find('.phpdebugbar-form-row').length > 0 &&
                    $form.find('[data-position-mode-field]').length === 0) {
                    addPositionModeToSettings.call(settingsWidget, $);
                    obs.disconnect();
                }
            });
            observer.observe(debugbarEl, { childList: true, subtree: true });
        }
    }

    function addPositionModeToSettings($) {
        if (!this.$el || !this.$el.length) {
            return;
        }

        if (this.$el.find('[data-position-mode-field]').length > 0) {
            return;
        }

        const settings = getSettings();
        const configPosition = (window.phpdebugbar_position_config || {}).position || 'bottom';
        const currentMode = settings.positionMode || configPosition;

        const $row = $('<div />').addClass('phpdebugbar-form-row').attr('data-position-mode-field', 'true');
        const $label = $('<div />').addClass('phpdebugbar-form-label').text('Position Mode');
        const $input = $('<div />').addClass('phpdebugbar-form-input');

        const $select = $('<select />')
            .append($('<option value="bottom">Bottom (Fixed)</option>'))
            .append($('<option value="floating">Floating (Draggable)</option>'))
            .val(currentMode)
            .on('change', function() {
                const newMode = $(this).val();
                storePositionMode(newMode);
                applyPositionMode(newMode);
            });

        $input.append($select);
        $row.append($label).append($input);

        const $resetRow = this.$el.find('.phpdebugbar-form-row').filter(function() {
            return $(this).find('button').length > 0;
        }).first();

        if ($resetRow.length > 0) {
            $row.insertBefore($resetRow);
        } else {
            this.$el.append($row);
        }
    }

    function storePositionMode(mode) {
        updateSettings('positionMode', mode);
        const debugbarEl = document.querySelector('div.phpdebugbar');
        if (debugbarEl) {
            debugbarEl.setAttribute('data-positionMode', mode);
        }
    }

    function applyPositionMode(mode) {
        const debugbarEl = document.querySelector('div.phpdebugbar');
        if (!debugbarEl) return;

        const wasFloating = debugbarEl.classList.contains('phpdebugbar-floating');

        debugbarEl.classList.remove('phpdebugbar-floating', 'phpdebugbar-ready',
            'phpdebugbar-snapped-bottom', 'phpdebugbar-snapped-top',
            'phpdebugbar-snapped-left', 'phpdebugbar-snapped-right',
            'phpdebugbar-snapped-top-left', 'phpdebugbar-snapped-top-right',
            'phpdebugbar-snapped-bottom-left', 'phpdebugbar-snapped-bottom-right');

        debugbarEl.style.cssText = '';

        if (window.phpdebugbar_draggable) {
            window.phpdebugbar_draggable.destroy();
            window.phpdebugbar_draggable = null;
        }

        if (wasFloating && mode !== 'floating') {
            try {
                localStorage.removeItem(STORAGE_KEY);
            } catch (e) {}
        }

        if (mode === 'floating') {
            const floatingOptions = (window.phpdebugbar_position_config || {}).floating || {};
            window.phpdebugbar_draggable = new DraggableDebugbar(debugbarEl, floatingOptions);
        }

        debugbarEl.setAttribute('data-positionMode', mode);
    }

    function applySavedPositionModeOnLoad(retries = 0) {
        const settings = getSettings();
        const configPosition = (window.phpdebugbar_position_config || {}).position || 'bottom';
        const mode = settings.positionMode || configPosition;

        const debugbarEl = document.querySelector('div.phpdebugbar');
        if (!debugbarEl) {
            if (retries < 50) {
                setTimeout(() => applySavedPositionModeOnLoad(retries + 1), 100);
            }
            return;
        }

        debugbarEl.setAttribute('data-positionMode', mode);

        if (mode === 'floating' && !debugbarEl.classList.contains('phpdebugbar-floating')) {
            window.phpdebugbar_position_config = window.phpdebugbar_position_config || {};
            window.phpdebugbar_position_config.position = 'floating';
            const floatingOptions = (window.phpdebugbar_position_config || {}).floating || {};
            window.phpdebugbar_draggable = new DraggableDebugbar(debugbarEl, floatingOptions);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            applySavedPositionModeOnLoad();
            hookSettingsWidget();
        });
    } else {
        applySavedPositionModeOnLoad();
        hookSettingsWidget();
    }
})();
