<?php

namespace Tests\Feature;

use App\Exceptions\Api\ApiException;
use App\Exceptions\Pterodactyl\PterodactylNotFoundException;
use App\Exceptions\Server\InsufficientCreditsException;
use App\Exceptions\Server\ServerLimitReachedException;
use App\Exceptions\Payment\InvoiceException;
use Illuminate\Foundation\Testing\TestCase;
use Tests\CreatesApplication;

class TestExceptionRendering extends TestCase
{
    use CreatesApplication;

    /**
     * Verify that an ApiException is rendered as JSON with its status code
     * for API requests.
     *
     * @return void
     */
    public function test_api_exception_renders_json_with_status_code(): void
    {
        $handler = new \App\Exceptions\Handler($this->app);

        $request = \Illuminate\Http\Request::create('/api/test', 'GET');
        $request->headers->set('Accept', 'application/json');

        $response = $handler->render(
            $request,
            new ApiException('Custom error', 418)
        );

        $this->assertSame(418, $response->getStatusCode());
        $this->assertSame(
            ['message' => 'Custom error'],
            $response->getData(true)
        );
    }

    /**
     * Verify the exception handler maps a PterodactylNotFoundException to a
     * 404 JSON response.
     *
     * @return void
     */
    public function test_pterodactyl_not_found_renders_404(): void
    {
        $handler = new \App\Exceptions\Handler($this->app);

        $request = \Illuminate\Http\Request::create('/api/test', 'GET');
        $request->headers->set('Accept', 'application/json');

        $response = $handler->render($request, new PterodactylNotFoundException());

        $this->assertSame(404, $response->getStatusCode());
        $this->assertStringContainsString('Resource does not exist', $response->getContent());
    }

    /**
     * Verify the exception handler maps an InsufficientCreditsException to a
     * 422 JSON response.
     *
     * @return void
     */
    public function test_insufficient_credits_renders_422(): void
    {
        $handler = new \App\Exceptions\Handler($this->app);

        $request = \Illuminate\Http\Request::create('/api/test', 'GET');
        $request->headers->set('Accept', 'application/json');

        $response = $handler->render($request, new InsufficientCreditsException());

        $this->assertSame(422, $response->getStatusCode());
    }

    /**
     * Verify the exception handler maps a ServerLimitReachedException to a
     * 422 JSON response.
     *
     * @return void
     */
    public function test_server_limit_reached_renders_422(): void
    {
        $handler = new \App\Exceptions\Handler($this->app);

        $request = \Illuminate\Http\Request::create('/api/test', 'GET');
        $request->headers->set('Accept', 'application/json');

        $response = $handler->render($request, new ServerLimitReachedException());

        $this->assertSame(422, $response->getStatusCode());
    }

    /**
     * Verify the exception handler maps a generic ApiException to its status
     * code.
     *
     * @return void
     */
    public function test_generic_api_exception_renders_status_code(): void
    {
        $handler = new \App\Exceptions\Handler($this->app);

        $request = \Illuminate\Http\Request::create('/api/test', 'GET');
        $request->headers->set('Accept', 'application/json');

        $response = $handler->render($request, new ApiException('Custom error', 418));

        $this->assertSame(418, $response->getStatusCode());
        $this->assertStringContainsString('Custom error', $response->getContent());
    }

    public function test_invoice_exception_renders_404_json(): void
    {
        $handler = new \App\Exceptions\Handler($this->app);

        $request = \Illuminate\Http\Request::create('/admin/test', 'GET');
        $request->headers->set('Accept', 'application/json');

        $response = $handler->render(
            $request,
            new InvoiceException('Invoice not found', 404)
        );

        $this->assertSame(404, $response->getStatusCode());
        $this->assertStringContainsString('Invoice not found', $response->getContent());
    }

    public function test_invoice_exception_renders_web_error_page(): void
    {
        $handler = new \App\Exceptions\Handler($this->app);

        $request = \Illuminate\Http\Request::create('/admin/test', 'GET');

        $response = $handler->render(
            $request,
            new InvoiceException('Invoice not found', 404)
        );

        $this->assertSame(404, $response->getStatusCode());
    }
}
