<?php

namespace Barryvdh\Debugbar\Tests;

use Illuminate\Routing\Router;
use Laravel\Dusk\Browser;

class FloatingDebugbarBrowserTest extends BrowserTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['env'] = 'local';
        $app['config']->set('debugbar.position', 'floating');
        $app['config']->set('debugbar.floating', [
            'initial_x' => null,
            'initial_y' => null,
            'remember_position' => true,
        ]);

        /** @var Router $router */
        $router = $app['router'];
        $this->addFloatingTestRoutes($router);

        \Orchestra\Testbench\Dusk\Options::withoutUI();
    }

    protected function addFloatingTestRoutes(Router $router)
    {
        $router->get('floating/test', function () {
            return '<html><head></head><body><h1>Floating Test Page</h1><div id="content">Test content for floating debugbar</div></body></html>';
        });
    }

    public function testFloatingModeInitializesCorrectly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('floating/test')
                ->waitFor('.phpdebugbar')
                ->assertPresent('.phpdebugbar.phpdebugbar-floating')
                ->assertPresent('.phpdebugbar.phpdebugbar-ready');
        });
    }

    public function testDebugbarHasDragHandle()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('floating/test')
                ->waitFor('.phpdebugbar.phpdebugbar-floating')
                ->assertPresent('.phpdebugbar-header.phpdebugbar-drag-handle');
        });
    }

    public function testDebugbarIsDraggable()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('floating/test')
                ->waitFor('.phpdebugbar.phpdebugbar-floating')
                ->pause(200);

            $initialLeft = $browser->script(
                'return document.querySelector(".phpdebugbar").getBoundingClientRect().left'
            )[0];

            // Step 1: Start drag
            $browser->script("
                var header = document.querySelector('.phpdebugbar-header');
                var rect = header.getBoundingClientRect();
                window._testDragStartX = rect.left + 50;
                window._testDragStartY = rect.top + 10;

                var mousedown = new MouseEvent('mousedown', {
                    bubbles: true, cancelable: true,
                    clientX: window._testDragStartX, clientY: window._testDragStartY
                });
                header.dispatchEvent(mousedown);
            ");

            $browser->pause(50);

            // Step 2: Move
            $browser->script("
                var mousemove = new MouseEvent('mousemove', {
                    bubbles: true, cancelable: true,
                    clientX: window._testDragStartX + 100, clientY: window._testDragStartY
                });
                document.dispatchEvent(mousemove);
            ");

            $browser->pause(50);

            // Step 3: End drag
            $browser->script("
                var mouseup = new MouseEvent('mouseup', { bubbles: true, cancelable: true });
                document.dispatchEvent(mouseup);
            ");

            $browser->pause(100);

            $newLeft = $browser->script(
                'return document.querySelector(".phpdebugbar").getBoundingClientRect().left'
            )[0];

            $this->assertNotEquals($initialLeft, $newLeft, 'Debugbar should have moved');
        });
    }

    public function testClickOnTabsDoesNotInitiateDrag()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('floating/test')
                ->waitFor('.phpdebugbar.phpdebugbar-floating')
                ->pause(200);

            $initialLeft = $browser->script(
                'return document.querySelector(".phpdebugbar").getBoundingClientRect().left'
            )[0];

            // Use JavaScript click - floating mode positioning makes native clicks unreliable
            $browser->script("document.querySelector('.phpdebugbar-tab[data-collector=\"messages\"]').click()");
            $browser->pause(100);

            $afterLeft = $browser->script(
                'return document.querySelector(".phpdebugbar").getBoundingClientRect().left'
            )[0];

            $this->assertEquals($initialLeft, $afterLeft, 'Position should not change when clicking tabs');
        });
    }

    public function testPositionIsSavedToLocalStorage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('floating/test')
                ->waitFor('.phpdebugbar.phpdebugbar-floating')
                ->pause(200);

            // Simulate drag via JavaScript (necessary for drag operations)
            $browser->script("
                var header = document.querySelector('.phpdebugbar-header');
                var rect = header.getBoundingClientRect();

                var mousedown = new MouseEvent('mousedown', {
                    bubbles: true, cancelable: true,
                    clientX: rect.left + 50, clientY: rect.top + 10
                });
                header.dispatchEvent(mousedown);

                var mousemove = new MouseEvent('mousemove', {
                    bubbles: true, cancelable: true,
                    clientX: rect.left - 100, clientY: rect.top - 50
                });
                document.dispatchEvent(mousemove);

                var mouseup = new MouseEvent('mouseup', { bubbles: true, cancelable: true });
                document.dispatchEvent(mouseup);
            ");

            $browser->pause(300);

            $saved = $browser->script(
                'return localStorage.getItem("phpdebugbar_floating_position")'
            )[0];

            $this->assertNotNull($saved, 'Position should be saved to localStorage');

            $position = json_decode($saved, true);
            $this->assertArrayHasKey('x', $position);
            $this->assertArrayHasKey('y', $position);
        });
    }

    public function testPositionIsRestoredOnPageLoad()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('floating/test')
                ->waitFor('.phpdebugbar.phpdebugbar-floating')
                ->script('localStorage.setItem("phpdebugbar_floating_position", JSON.stringify({x: 100, y: 80, snapped: false, snapZone: null, timestamp: Date.now()}))');

            $browser->refresh()
                ->waitFor('.phpdebugbar.phpdebugbar-floating')
                ->pause(300);

            $left = $browser->script(
                'return document.querySelector(".phpdebugbar").getBoundingClientRect().left'
            )[0];

            $this->assertEqualsWithDelta(100, $left, 20, 'Position should be restored from localStorage');
        });
    }

    public function testSnapPreviewAppearsNearEdge()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('floating/test')
                ->waitFor('.phpdebugbar.phpdebugbar-floating')
                ->pause(200);

            // Simulate drag near edge (JavaScript required for drag simulation)
            $browser->script("
                var header = document.querySelector('.phpdebugbar-header');
                var rect = header.getBoundingClientRect();

                var mousedown = new MouseEvent('mousedown', {
                    bubbles: true, cancelable: true,
                    clientX: rect.left + 50, clientY: rect.top + 10
                });
                header.dispatchEvent(mousedown);

                var mousemove = new MouseEvent('mousemove', {
                    bubbles: true, cancelable: true,
                    clientX: 30, clientY: rect.top
                });
                document.dispatchEvent(mousemove);
            ");

            $browser->pause(150);

            $previewExists = $browser->script(
                'return document.querySelector(".phpdebugbar-snap-preview.phpdebugbar-snap-preview-active") !== null'
            )[0];

            $browser->script("
                var mouseup = new MouseEvent('mouseup', { bubbles: true, cancelable: true });
                document.dispatchEvent(mouseup);
            ");

            $this->assertTrue($previewExists, 'Snap preview should appear when near edge');
        });
    }

    public function testSnapToBottomZoneViaScript()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('floating/test')
                ->waitFor('.phpdebugbar.phpdebugbar-floating')
                ->pause(200);

            $debug = $browser->script("
                var d = window.phpdebugbar_draggable;
                var zones = Object.keys(d.constructor.toString().match(/SNAP_ZONES/g) || []);
                return {
                    hasSnapTo: typeof d.snapTo === 'function',
                    optionsEnableSnapping: d.options.enableSnapping
                }
            ")[0];

            $this->assertTrue($debug['hasSnapTo'], 'snapTo method should exist');
            $this->assertTrue($debug['optionsEnableSnapping'], 'snapping should be enabled');

            $snapResult = $browser->script("
                var d = window.phpdebugbar_draggable;
                window._snapDebug = { before: { isSnapped: d.isSnapped, width: document.querySelector('.phpdebugbar').style.width }};
                d.snapTo('bottom');
                window._snapDebug.immediateAfter = { isSnapped: d.isSnapped, width: document.querySelector('.phpdebugbar').style.width };
                return window._snapDebug;
            ")[0];

            $browser->pause(400);

            $state = $browser->script("
                var db = document.querySelector('.phpdebugbar');
                window._snapDebug.afterPause = {
                    isSnapped: window.phpdebugbar_draggable.isSnapped,
                    snapZone: window.phpdebugbar_draggable.currentSnapZone,
                    width: db.style.width,
                    isMinimized: db.classList.contains('phpdebugbar-minimized'),
                    classes: db.className
                };
                return window._snapDebug;
            ")[0];

            $this->assertTrue($state['afterPause']['isSnapped'], 'Should be snapped. Debug: ' . json_encode($state));
            $this->assertEquals('bottom', $state['afterPause']['snapZone'], 'Should be snapped to bottom');
            $this->assertEquals('100%', $state['afterPause']['width'], 'Width should be 100%');
        });
    }

    public function testPositionModeSettingAppears()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('floating/test')
                ->waitFor('.phpdebugbar.phpdebugbar-floating')
                ->pause(300)
                ->click('.phpdebugbar-tab-settings')
                ->pause(800);

            $hasPositionMode = $browser->script(
                'return document.querySelector("[data-position-mode-field]") !== null'
            )[0];

            $this->assertTrue($hasPositionMode, 'Position Mode field should appear in settings');
        });
    }

    public function testSwitchFromFloatingToBottomMode()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('floating/test')
                ->waitFor('.phpdebugbar.phpdebugbar-floating')
                ->pause(300)
                ->click('.phpdebugbar-tab-settings')
                ->pause(800);

            // Select dropdown value via JavaScript (necessary for custom dropdowns)
            $browser->script("
                var select = document.querySelector('[data-position-mode-field] select');
                if (select) {
                    select.value = 'bottom';
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                }
            ");

            $browser->pause(400);

            $isFloating = $browser->script(
                'return document.querySelector(".phpdebugbar").classList.contains("phpdebugbar-floating")'
            )[0];

            $this->assertFalse($isFloating, 'Debugbar should not have floating class after switching to bottom');
        });
    }

    public function testDragBoundsAreRespected()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('floating/test')
                ->waitFor('.phpdebugbar.phpdebugbar-floating')
                ->pause(200);

            // Simulate extreme drag via JavaScript
            $browser->script("
                var header = document.querySelector('.phpdebugbar-header');
                var rect = header.getBoundingClientRect();

                var mousedown = new MouseEvent('mousedown', {
                    bubbles: true, cancelable: true,
                    clientX: rect.left + 50, clientY: rect.top + 10
                });
                header.dispatchEvent(mousedown);

                var mousemove = new MouseEvent('mousemove', {
                    bubbles: true, cancelable: true,
                    clientX: -2000, clientY: rect.top
                });
                document.dispatchEvent(mousemove);

                var mouseup = new MouseEvent('mouseup', { bubbles: true, cancelable: true });
                document.dispatchEvent(mouseup);
            ");

            $browser->pause(100);

            $left = $browser->script(
                'return document.querySelector(".phpdebugbar").getBoundingClientRect().left'
            )[0];

            $width = $browser->script(
                'return document.querySelector(".phpdebugbar").getBoundingClientRect().width'
            )[0];

            $this->assertGreaterThan(-$width + 50, $left, 'Debugbar should stay at least 50px visible');
        });
    }

    public function testFOUCPreventionStyleIsRemovedAfterReady()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('floating/test')
                ->waitFor('.phpdebugbar.phpdebugbar-ready')
                ->pause(100);

            $foucStyleExists = $browser->script(
                'return document.getElementById("phpdebugbar-fouc-fix") !== null'
            )[0];

            $this->assertFalse($foucStyleExists, 'FOUC prevention style should be removed after initialization');
        });
    }

    public function testDebugbarHasReadyClassAfterInit()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('floating/test')
                ->waitFor('.phpdebugbar.phpdebugbar-floating')
                ->assertPresent('.phpdebugbar.phpdebugbar-ready');
        });
    }

    public function testSnappedStateIsPersisted()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('floating/test')
                ->waitFor('.phpdebugbar.phpdebugbar-floating')
                ->script('localStorage.setItem("phpdebugbar_floating_position", JSON.stringify({x: 0, y: 0, snapped: true, snapZone: "bottom", timestamp: Date.now()}))');

            $browser->refresh()
                ->waitFor('.phpdebugbar.phpdebugbar-floating')
                ->pause(400);

            $width = $browser->script(
                'return document.querySelector(".phpdebugbar").style.width'
            )[0];

            $this->assertEquals('100%', $width, 'Snapped state should be restored');
        });
    }

    public function testMinimizeMaximizeWorksInFloatingMode()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('floating/test')
                ->waitFor('.phpdebugbar.phpdebugbar-floating')
                ->pause(200);

            // Use JavaScript click - floating mode positioning makes native clicks unreliable
            $browser->script("document.querySelector('.phpdebugbar-minimize-btn').click()");
            $browser->pause(300);

            $isMinimized = $browser->script(
                'return document.querySelector(".phpdebugbar").classList.contains("phpdebugbar-minimized")'
            )[0];

            $this->assertTrue($isMinimized, 'Debugbar should be minimized');

            $browser->script("document.querySelector('.phpdebugbar-maximize-btn').click()");
            $browser->pause(300);

            $isMinimizedAfter = $browser->script(
                'return document.querySelector(".phpdebugbar").classList.contains("phpdebugbar-minimized")'
            )[0];

            $this->assertFalse($isMinimizedAfter, 'Debugbar should not be minimized after restore');
        });
    }

    public function testPositionConfigIsInjectedAsInlineScript()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('floating/test')
                ->waitFor('.phpdebugbar');

            $config = $browser->script(
                'return window.phpdebugbar_position_config'
            )[0];

            $this->assertNotNull($config, 'Position config should be available on window');
            $this->assertEquals('floating', $config['position']);
        });
    }

    protected function tearDown(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->script('localStorage.clear()');
        });

        parent::tearDown();
    }
}
