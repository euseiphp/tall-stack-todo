<?php

namespace Tests\Feature\Http\Livewire\ToDo;

use App\Http\Livewire\ToDo\Overview;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OverviewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_be_able_to_redirect_to_login_if_not_auth()
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function it_should_be_able_to_redirect_to_dashboard_if_is_auth()
    {
        $this->actingAs($user = User::factory()->create());

        $this->get(route('login'))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_should_be_able_to_see_only_your_todos()
    {
        $foo = User::factory()->create();

        $todo = Todo::factory()
            ->for($foo)
            ->create();

        $this->actingAs($bar = User::factory()->create());

        Livewire::test(Overview::class)
            ->assertDontSee($todo->title);
    }

    /** @test */
    public function it_should_be_able_to_create_todo()
    {
        $this->actingAs($user = User::factory()->create());
        $title = 'Fazer compras';

        Livewire::test(Overview::class)
            ->set('title', $title)
            ->call('create')
            ->assertHasNoErrors()
            ->assertSuccessful()
            ->assertSee($title);

        $this->assertDatabaseHas('todos', [
            'user_id' => $user->id,
            'title' => $title,
        ]);
    }

    /** @test */
    public function it_should_be_able_to_be_completed()
    {
        $this->actingAs($user = User::factory()->create());

        $todo = Todo::factory()
            ->for($user)
            ->uncompleted()
            ->create();

        Livewire::test(Overview::class)
            ->call('completed', $todo)
            ->assertSuccessful();

        $this->assertTrue($todo->is_completed);
    }

    /** @test */
    public function it_should_be_able_to_be_destroyed()
    {
        $this->actingAs($user = User::factory()->create());

        $todo = Todo::factory()
            ->for($user)
            ->create();

        Livewire::test(Overview::class)
            ->call('destroy', $todo)
            ->assertSuccessful();

        $this->assertModelMissing($todo);
    }

    /** @test */
    public function it_should_not_be_able_to_create_todo_with_title_in_use()
    {
        $this->actingAs($user = User::factory()->create());

        $todo = Todo::factory()
            ->for($user)
            ->create([
                'title' => 'Levar o carro para lavar',
            ]);

        Livewire::test(Overview::class)
            ->set('title', $todo->title)
            ->call('create')
            ->assertHasErrors([
                'title' => 'unique',
            ]);

        $this->assertDatabaseCount('todos', 1);
    }
}
