<?php

namespace Spatie\WebTinker\Tests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Spatie\WebTinker\Http\Middleware\Authorize;

class AuthorizeTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        config()->set('web-tinker.enabled', true);

        app()->detectEnvironment(function() {
            return 'local';
        });

        Route::get('/test', function() {
            return 'ok';
        })->middleware(Authorize::class);
    }

    /** @test */
    public function it_will_allow_requests_if_it_is_enabled()
    {
        $this->get('/test')->assertStatus(200);
    }

    /** @test */
    public function it_will_not_allow_requests_if_the_gate_does_not_allow_it()
    {
        Gate::define('viewWebTinker', function() {
            return false;
        });

        $this->get('/test')->assertStatus(403);
    }

    /** @test */
    public function it_will_not_allow_requests_if_it_is_disabled_even_it_the_gate_allows_it()
    {
        Gate::define('viewWebTinker', function() {
            return true;
        });

        config()->set('web-tinker.enabled', false);

        $this->get('/test')->assertStatus(403);
    }
}
