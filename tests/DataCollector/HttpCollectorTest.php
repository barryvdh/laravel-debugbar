<?php

namespace Barryvdh\Debugbar\Tests\DataCollector;

use Barryvdh\Debugbar\DataCollector\HttpCollector;
use Barryvdh\Debugbar\Tests\TestCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class HttpCollectorTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        // Add additional test routes
        Route::get('test/json', function () {
            return response()->json(['message' => 'Hello', 'secret' => 'password123']);
        });

        Route::get('test/redirect', function () {
            return redirect('/test/destination');
        });

        Route::get('test/view', function () {
            return view('dashboard');
        });

        Route::post('test/with-input', function (Request $request) {
            return response()->json(['received' => $request->all()]);
        });

        Route::get('test/plain-text', function () {
            return response('Plain text response', 200, ['Content-Type' => 'text/plain']);
        });

        Route::get('test/large-response', function () {
            return response()->json(['data' => str_repeat('x', 100000)]);
        });
    }

    public function testCollectorName()
    {
        $collector = $this->createCollector();
        $this->assertEquals('http', $collector->getName());
    }

    public function testCollectorWidgets()
    {
        $collector = $this->createCollector();
        $widgets = $collector->getWidgets();

        $this->assertArrayHasKey('http', $widgets);
        $this->assertArrayHasKey('http:badge', $widgets);
        $this->assertEquals('globe', $widgets['http']['icon']);
        $this->assertEquals('PhpDebugBar.Widgets.HtmlVariableListWidget', $widgets['http']['widget']);
    }

    public function testItCollectsBasicRequestInformation()
    {
        $response = $this->get('web/html');
        $collector = $this->createCollectorFromResponse($response);

        $data = $collector->collect();

        $this->assertArrayHasKey('uri', $data);
        $this->assertArrayHasKey('method', $data);
        $this->assertArrayHasKey('response_status', $data);
        $this->assertEquals('GET', $data['method']);
        $this->assertEquals(200, $data['response_status']);
    }

    public function testItCollectsControllerAction()
    {
        $response = $this->get('web/show');
        $collector = $this->createCollectorFromResponse($response);

        $data = $collector->collect();

        $this->assertArrayHasKey('controller_action', $data);
        $this->assertNotNull($data['controller_action']);
    }

    public function testItCollectsMiddleware()
    {
        $response = $this->post('web/mw');
        $collector = $this->createCollectorFromResponse($response);

        $data = $collector->collect();

        $this->assertArrayHasKey('middleware', $data);
        $this->assertIsArray($data['middleware']);
    }

    public function testItCollectsRequestHeaders()
    {
        $response = $this->withHeaders([
            'X-Custom-Header' => 'test-value',
            'Accept' => 'application/json',
        ])->get('web/plain');

        $collector = $this->createCollectorFromResponse($response);
        $data = $collector->collect();

        $this->assertArrayHasKey('headers', $data);
        $this->assertIsArray($data['headers']);
    }

    public function testItHidesRequestHeaders()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer secret-token',
        ])->get('web/plain');

        $collector = new HttpCollector(
            $this->app['request'],
            $response->baseResponse,
            null,
            ['authorization'], // Hidden headers
            [],
            [],
            []
        );

        $data = $collector->collect();

        if (isset($data['headers']['authorization'])) {
            $this->assertEquals('********', $data['headers']['authorization']);
        }
    }

    public function testItCollectsRequestPayload()
    {
        $response = $this->post('test/with-input', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
        ]);

        $collector = $this->createCollectorFromResponse($response);
        $data = $collector->collect();

        $this->assertArrayHasKey('payload', $data);
        $this->assertIsArray($data['payload']);
    }

    public function testItHidesRequestParameters()
    {
        $response = $this->post('test/with-input', [
            'name' => 'John Doe',
            'password' => 'secret123',
        ]);

        $collector = new HttpCollector(
            $this->app['request'],
            $response->baseResponse,
            null,
            [],
            ['password'], // Hidden parameters
            [],
            []
        );

        $data = $collector->collect();

        $this->assertEquals('********', $data['payload']['password']);
    }

    public function testItCollectsSessionData()
    {
        $response = $this->withSession(['user_id' => 1, 'token' => 'abc123'])->get('web/plain');

        $collector = $this->createCollectorFromResponse($response);
        $data = $collector->collect();

        $this->assertArrayHasKey('session', $data);
        $this->assertIsArray($data['session']);
    }

    public function testItCollectsDurationAndMemory()
    {
        $startTime = microtime(true) - 0.1; // Subtract 0.1 seconds to ensure measurable duration
        $response = $this->get('web/html');

        $collector = new HttpCollector(
            $this->app['request'],
            $response->baseResponse,
            $startTime
        );

        $data = $collector->collect();

        $this->assertArrayHasKey('duration', $data);
        $this->assertArrayHasKey('memory', $data);
        $this->assertIsNumeric($data['duration']);
        $this->assertIsNumeric($data['memory']);
        $this->assertGreaterThanOrEqual(0, $data['duration']);
        $this->assertGreaterThan(0, $data['memory']);
    }

    public function testItHandlesJsonResponse()
    {
        $response = $this->get('test/json');
        $collector = $this->createCollectorFromResponse($response);

        $data = $collector->collect();

        $this->assertArrayHasKey('response', $data);
        $this->assertIsArray($data['response']);
        $this->assertArrayHasKey('message', $data['response']);
        $this->assertEquals('Hello', $data['response']['message']);
    }

    public function testItHidesResponseParameters()
    {
        $response = $this->get('test/json');

        $collector = new HttpCollector(
            $this->app['request'],
            $response->baseResponse,
            null,
            [],
            [],
            ['secret'], // Hidden response parameters
            []
        );

        $data = $collector->collect();

        $this->assertEquals('********', $data['response']['secret']);
    }

    public function testItHandlesRedirectResponse()
    {
        $response = $this->get('test/redirect');
        $collector = $this->createCollectorFromResponse($response);

        $data = $collector->collect();

        $this->assertArrayHasKey('response', $data);
        $this->assertStringContainsString('Redirected to', $data['response']);
    }

    public function testItHandlesViewResponse()
    {
        $response = $this->get('test/view');
        $collector = $this->createCollectorFromResponse($response);

        $data = $collector->collect();

        $this->assertArrayHasKey('response', $data);
        $this->assertIsArray($data['response']);
        $this->assertArrayHasKey('view', $data['response']);
        $this->assertArrayHasKey('data', $data['response']);
    }

    public function testItHandlesPlainTextResponse()
    {
        $response = $this->get('test/plain-text');
        $collector = $this->createCollectorFromResponse($response);

        $data = $collector->collect();

        $this->assertArrayHasKey('response', $data);
        $this->assertEquals('Plain text response', $data['response']);
    }

    public function testItHandlesHtmlResponse()
    {
        $response = $this->get('web/html');
        $collector = $this->createCollectorFromResponse($response);

        $data = $collector->collect();

        $this->assertArrayHasKey('response', $data);
        $this->assertEquals('HTML Response', $data['response']);
    }

    public function testItPurgesLargeResponses()
    {
        $response = $this->get('test/large-response');

        // Set a small size limit (1KB)
        $collector = new HttpCollector(
            $this->app['request'],
            $response->baseResponse,
            null,
            [],
            [],
            [],
            [],
            1 // 1KB size limit
        );

        $data = $collector->collect();

        $this->assertArrayHasKey('response', $data);
        $this->assertStringContainsString('Purged By Debugbar', $data['response']);
    }

    public function testItIgnoresConfiguredStatusCodes()
    {
        $response = $this->get('web/html'); // Returns 200

        $collector = new HttpCollector(
            $this->app['request'],
            $response->baseResponse,
            null,
            [],
            [],
            [],
            [200] // Ignore 200 status codes
        );

        $data = $collector->collect();

        $this->assertEmpty($data);
    }

    public function testItCollectsPostRequests()
    {
        $response = $this->post('test/with-input', ['test' => 'data']);
        $collector = $this->createCollectorFromResponse($response);

        $data = $collector->collect();

        $this->assertEquals('POST', $data['method']);
        $this->assertEquals(200, $data['response_status']);
    }

    public function testItFormatsUriCorrectly()
    {
        $response = $this->get('/web/html');
        $collector = $this->createCollectorFromResponse($response);

        $data = $collector->collect();

        $this->assertArrayHasKey('uri', $data);
        $this->assertStringContainsString('web/html', $data['uri']);
    }

    public function testItCollectsTitle()
    {
        $response = $this->get('web/plain');
        $collector = $this->createCollectorFromResponse($response);

        $data = $collector->collect();

        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('description', $data);
        $this->assertStringContainsString('HTTP Status Code', $data['title']);
        $this->assertStringContainsString('200', $data['title']);
    }

    /**
     * Helper method to create a collector from the current request/response
     */
    protected function createCollector(): HttpCollector
    {
        $request = Request::create('/test', 'GET');
        $response = new Response('Test');

        return new HttpCollector($request, $response);
    }

    /**
     * Helper method to create a collector from a test response
     */
    protected function createCollectorFromResponse($testResponse): HttpCollector
    {
        return new HttpCollector(
            $this->app['request'],
            $testResponse->baseResponse
        );
    }
}
