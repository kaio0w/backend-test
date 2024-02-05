<?php

namespace Tests\Feature;

use App\Models\Redirect;
use App\Models\RedirectLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;



use Tests\TestCase;

class RedirectTest extends TestCase
{
    use RefreshDatabase;

    public function testRedirectIsWorking()
    {
        $redirect = Redirect::factory()->create([
            'url' => 'https://www.linkedin.com/in/kaioxsilva/', 
        ]);

        $response = $this->get("/r/{$redirect->code}");

        $response->assertRedirect($redirect->url);

       
        $this->assertDatabaseHas('redirect_logs', [
            'redirect_id' => $redirect->id,
            'ip' => $this->getTestIpAddress(),
        ]);
    }

    public function testStatsEndpointReturnsCorrectData()
    {
        $redirect = Redirect::factory()->create();
        RedirectLog::factory()->count(5)->create(['redirect_id' => $redirect->id]);

        $response = $this->get("/api/redirects/{$redirect->code}/stats");

        $response->assertJsonStructure([
            'total_access',
            'unique_access',
            'top_referrers',
            'last_10_days',
        ]);
    }

   

    private function getTestIpAddress()
    {
      
        return '127.0.0.1';
    }

    public function testUpdateRedirect()
    {
        $redirect = Redirect::factory()->create();

        $response = $this->put("/api/redirects/{$redirect->code}", [
            'url' => 'https://updated-url.com',
            'status' => 'active',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('redirects', [
            'id' => $redirect->id,
            'url' => 'https://updated-url.com',
            'status' => 'active',
        ]);
    }

    public function testDeleteRedirect()
    {
        $redirect = Redirect::factory()->create();

        $response = $this->delete("/api/redirects/{$redirect->code}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('redirects', ['id' => $redirect->id]);
    }
}