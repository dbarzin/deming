<?php

use App\Models\Measure;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->user  = User::factory()->user()->create();
});

test('guest is redirected to login', function () {
    $this->get('/groups')->assertRedirect('/login');
});

test('admin can list groups', function () {
    UserGroup::factory()->count(3)->create();
    $this->actingAs($this->admin)->get('/groups')->assertStatus(200);
});

test('non-admin cannot list groups', function () {
    $this->actingAs($this->user)->get('/groups')->assertStatus(403);
});

test('admin can access create form', function () {
    $this->actingAs($this->admin)->get('/groups/create')->assertStatus(200);
});

test('non-admin cannot access create form', function () {
    $this->actingAs($this->user)->get('/groups/create')->assertStatus(403);
});

test('admin can create a group', function () {
    $this->actingAs($this->admin)
        ->post('/groups', [
            'name' => 'Security Team',
            'description' => 'Security team members',
            'type' => 'business',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('user_groups', ['name' => 'Security Team']);
});

test('non-admin cannot create a group', function () {
    $this->actingAs($this->user)
        ->post('/groups', ['name' => 'Test', 'description' => 'Test'])
        ->assertStatus(403);
});

test('admin can view a group', function () {
    $group = UserGroup::factory()->create();
    $this->actingAs($this->admin)->get("/groups/{$group->id}")->assertStatus(200);
});

test('non-admin cannot view a group', function () {
    $group = UserGroup::factory()->create();
    $this->actingAs($this->user)->get("/groups/{$group->id}")->assertStatus(403);
});

test('admin can edit a group', function () {
    $group = UserGroup::factory()->create();
    $this->actingAs($this->admin)->get("/groups/{$group->id}/edit")->assertStatus(200);
});

test('admin can update a group', function () {
    $group = UserGroup::factory()->create();

    $this->actingAs($this->admin)
        ->put("/groups/{$group->id}", [
            'name' => 'Updated Group',
            'description' => 'Updated description',
            'type' => 'business',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('user_groups', ['id' => $group->id, 'name' => 'Updated Group']);
});

test('admin can delete a group', function () {
    $group = UserGroup::factory()->create();

    $this->actingAs($this->admin)
        ->delete("/groups/{$group->id}")
        ->assertRedirect();

    $this->assertDatabaseMissing('user_groups', ['id' => $group->id]);
});

// --- Group type (business / security)

test('migration sets existing groups to business', function () {
    $migration = require database_path('migrations/2026_10_17_000001_add_type_to_user_groups.php');
    $migration->down();
    expect(Schema::hasColumn('user_groups', 'type'))->toBeFalse();

    $id = DB::table('user_groups')->insertGetId(['name' => 'Legacy group']);
    $migration->up();

    expect(DB::table('user_groups')->where('id', $id)->value('type'))->toBe(UserGroup::TYPE_BUSINESS);
});

test('admin can create a security group', function () {
    $this->actingAs($this->admin)
        ->post('/groups', ['name' => 'SecOps', 'type' => 'security'])
        ->assertRedirect();

    $this->assertDatabaseHas('user_groups', ['name' => 'SecOps', 'type' => UserGroup::TYPE_SECURITY]);
});

test('admin can change the type of an existing group', function () {
    $group = UserGroup::factory()->create();

    $this->actingAs($this->admin)
        ->put("/groups/{$group->id}", ['name' => $group->name, 'type' => 'security'])
        ->assertRedirect();

    expect($group->fresh()->type)->toBe(UserGroup::TYPE_SECURITY);
});

test('an unknown group type is rejected', function () {
    $group = UserGroup::factory()->create();

    $this->actingAs($this->admin)
        ->post('/groups', ['name' => 'Bad', 'type' => 'admin'])
        ->assertSessionHasErrors('type');
    $this->actingAs($this->admin)
        ->put("/groups/{$group->id}", ['name' => $group->name, 'type' => 'admin'])
        ->assertSessionHasErrors('type');

    $this->assertDatabaseMissing('user_groups', ['name' => 'Bad']);
    expect($group->fresh()->type)->toBe(UserGroup::TYPE_BUSINESS);
});

test('a forged request from a non-admin cannot set the group type', function () {
    $group = UserGroup::factory()->create();

    $this->actingAs($this->user)
        ->post('/groups', ['name' => 'Forged', 'type' => 'security'])
        ->assertStatus(403);
    $this->actingAs($this->user)
        ->put("/groups/{$group->id}", ['name' => $group->name, 'type' => 'security'])
        ->assertStatus(403);

    $this->assertDatabaseMissing('user_groups', ['name' => 'Forged']);
    expect($group->fresh()->type)->toBe(UserGroup::TYPE_BUSINESS);
});

test('updating a group keeps its completed and proposed measures', function () {
    $group = UserGroup::factory()->create();
    $open = Measure::factory()->create(['status' => 0]);
    $proposed = Measure::factory()->create(['status' => 1, 'realisation_date' => now()->format('Y-m-d')]);
    $done = Measure::factory()->create(['status' => 2, 'realisation_date' => now()->format('Y-m-d')]);
    $group->measures()->attach([$open->id, $proposed->id, $done->id]);

    // The form only lists open measures; none selected here
    $this->actingAs($this->admin)
        ->put("/groups/{$group->id}", ['name' => $group->name, 'type' => 'business', 'measures' => []])
        ->assertRedirect();

    $linked = DB::table('measure_user_group')->where('user_group_id', $group->id)->pluck('measure_id')->all();
    expect($linked)->toEqualCanonicalizing([$proposed->id, $done->id]);
});
