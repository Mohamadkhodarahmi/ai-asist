<?php

namespace Tests\Feature\Business;

use App\Livewire\Business\CreateBusiness;
use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateBusinessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_render_business_creation_component()
    {
        $this->actingAs(User::factory()->create());
        
        $this->get('/dashboard')
            ->assertSeeLivewire(CreateBusiness::class);
    }

    /** @test */
    public function it_can_create_a_business()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreateBusiness::class)
            ->set('name', 'Test Business')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect('/dashboard');

        $this->assertDatabaseHas('businesses', [
            'name' => 'Test Business'
        ]);
    }

    /** @test */
    public function business_name_is_required()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreateBusiness::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    /** @test */
    public function business_name_must_be_at_least_two_characters()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreateBusiness::class)
            ->set('name', 'A')
            ->call('save')
            ->assertHasErrors(['name' => 'min']);
    }
}
