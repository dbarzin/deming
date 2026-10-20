<?php

use App\Models\Action;
use App\Models\Control;
use App\Models\Risk;
use App\Models\User;

beforeEach(function () {
    $this->admin   = User::factory()->admin()->create();
    $this->user    = User::factory()->user()->create();
    $this->auditor = User::factory()->auditor()->create();
});

test('guest is redirected to login', function () {
    $this->get('/actions')->assertRedirect('/login');
});

test('admin can list actions', function () {
    Action::factory()->count(3)->create();
    $this->actingAs($this->admin)->get('/actions')->assertStatus(200);
});

test('the action list column order puts clauses after type and remediation after name', function () {
    Action::factory()->create(['name' => 'Patch servers', 'remediation' => 'Apply the vendor fix']);
    $this->actingAs($this->admin)->get('/actions')
        ->assertStatus(200)
        ->assertSeeInOrder([
            '>' . trans('cruds.action.fields.type') . '</th>',
            '<th class="dt-hidden">' . trans('cruds.action.fields.clauses') . '</th>',
            '>' . trans('cruds.action.fields.status') . '</th>',
            '>' . trans('cruds.action.fields.name') . '</th>',
            '<th class="dt-hidden">' . trans('cruds.action.fields.remediation') . '</th>',
            '>' . trans('cruds.action.fields.scope') . '</th>',
            'Patch servers',
            'Apply the vendor fix',
        ], false);
});

test('auditor can list actions', function () {
    $this->actingAs($this->auditor)->get('/actions')->assertStatus(200);
});

test('api user cannot list actions', function () {
    $this->actingAs(User::factory()->apiUser()->create())
        ->get('/actions')
        ->assertStatus(403);
});

test('admin can access create form', function () {
    $this->actingAs($this->admin)->get('/action/create')->assertStatus(200);
});

test('auditor cannot access create form', function () {
    $this->actingAs($this->auditor)->get('/action/create')->assertStatus(403);
});

test('admin can create an action', function () {
    $this->actingAs($this->admin)
        ->post('/action/store', [
            'name' => 'Fix authentication vulnerability',
            'type' => '1',
            'due_date' => '2026-12-01',
        ])
        ->assertRedirect('/actions');

    $this->assertDatabaseHas('actions', ['name' => 'Fix authentication vulnerability']);
});

test('non-admin user can create an action', function () {
    $this->actingAs($this->user)
        ->post('/action/store', [
            'name' => 'Patch known vulnerability',
            'type' => '2',
        ])
        ->assertRedirect('/actions');

    $this->assertDatabaseHas('actions', ['name' => 'Patch known vulnerability']);
});

test('auditor cannot create an action', function () {
    $this->actingAs($this->auditor)
        ->post('/action/store', ['name' => 'Test Action'])
        ->assertStatus(403);
});

test('admin can view an action', function () {
    $action = Action::factory()->create();
    $this->actingAs($this->admin)->get("/action/show/{$action->id}")->assertStatus(200);
});

test('auditor can view an action', function () {
    $action = Action::factory()->create();
    $this->actingAs($this->auditor)->get("/action/show/{$action->id}")->assertStatus(200);
});

test('api user cannot view an action', function () {
    $action = Action::factory()->create();
    $this->actingAs(User::factory()->apiUser()->create())
        ->get("/action/show/{$action->id}")
        ->assertStatus(403);
});

test('admin can edit an action', function () {
    $action = Action::factory()->create();
    $this->actingAs($this->admin)->get("/action/edit/{$action->id}")->assertStatus(200);
});

test('auditor can access edit form', function () {
    $action = Action::factory()->create();
    $this->actingAs($this->auditor)->get("/action/edit/{$action->id}")->assertStatus(200);
});

test('edit form pre-selects only the controls linked to this action', function () {
    $linkedControl = Control::factory()->create();
    $otherControl = Control::factory()->create();

    $action = Action::factory()->create();
    $action->controls()->sync([$linkedControl->id]);

    // Another action links a different control: must not leak into the first action's selection.
    $otherAction = Action::factory()->create();
    $otherAction->controls()->sync([$otherControl->id]);

    $this->actingAs($this->admin)
        ->get("/action/edit/{$action->id}")
        ->assertStatus(200)
        ->assertViewHas('measures', [$linkedControl->id]);
});

test('admin can save an action', function () {
    $action = Action::factory()->create();

    $this->actingAs($this->admin)
        ->post('/action/save', [
            'id' => $action->id,
            'name' => 'Updated Action Name',
            'status' => 0,
        ])
        ->assertRedirect("/action/show/{$action->id}");

    $this->assertDatabaseHas('actions', ['id' => $action->id, 'name' => 'Updated Action Name']);
});

test('non-admin user can save an action', function () {
    $action = Action::factory()->create();

    $this->actingAs($this->user)
        ->post('/action/save', [
            'id' => $action->id,
            'name' => 'User Updated Action',
            'status' => 0,
        ])
        ->assertRedirect("/action/show/{$action->id}");
});

test('auditor cannot save an action', function () {
    $action = Action::factory()->create();
    $this->actingAs($this->auditor)
        ->post('/action/save', ['id' => $action->id, 'name' => 'Updated'])
        ->assertStatus(403);
});

test('admin can delete an action', function () {
    $action = Action::factory()->create();
    $this->actingAs($this->admin)
        ->post('/action/delete', ['id' => $action->id])
        ->assertRedirect();

    $this->assertDatabaseMissing('actions', ['id' => $action->id]);
});

test('auditor cannot delete an action', function () {
    $action = Action::factory()->create();
    $this->actingAs($this->auditor)
        ->post('/action/delete', ['id' => $action->id])
        ->assertStatus(403);
});

test('admin can export actions', function () {
    Action::factory()->count(2)->create();
    $this->actingAs($this->admin)->get('/export/actions')->assertStatus(200);
});

test('admin can create an action linked to risks', function () {
    $risk = Risk::factory()->create();

    $this->actingAs($this->admin)
        ->post('/action/store', ['name' => 'Harden servers', 'risks' => [$risk->id]])
        ->assertRedirect('/actions');

    $action = Action::where('name', 'Harden servers')->firstOrFail();
    expect($action->risks->pluck('id')->all())->toBe([$risk->id]);
});

test('saving an action replaces its risks and the edit form pre-selects them', function () {
    $oldRisk = Risk::factory()->create();
    $newRisk = Risk::factory()->create();
    $action = Action::factory()->create();
    $action->risks()->sync([$oldRisk->id]);

    $this->actingAs($this->admin)
        ->post('/action/save', ['id' => $action->id, 'name' => $action->name, 'status' => 0, 'risks' => [$newRisk->id]])
        ->assertRedirect("/action/show/{$action->id}");

    $this->actingAs($this->admin)
        ->get("/action/edit/{$action->id}")
        ->assertStatus(200)
        ->assertViewHas('risks', [$newRisk->id]);
});

test('the action list and detail show the linked risks but not the deleted ones', function () {
    $risk = Risk::factory()->create(['name' => 'Ransomware on workstations']);
    $deletedRisk = Risk::factory()->create(['name' => 'Retired risk']);
    $action = Action::factory()->create(['status' => 0]);
    $action->risks()->sync([$risk->id, $deletedRisk->id]);
    $deletedRisk->delete();

    $this->actingAs($this->admin)->get('/actions?status=0')
        ->assertStatus(200)
        ->assertSee('Ransomware on workstations')
        ->assertDontSee('Retired risk');

    $this->actingAs($this->admin)->get("/action/show/{$action->id}")
        ->assertStatus(200)
        ->assertSee('Ransomware on workstations')
        ->assertDontSee('Retired risk');
});

test('the action detail hides the clauses and risks labels when the action has none', function () {
    $action = Action::factory()->create();

    $this->actingAs($this->admin)->get("/action/show/{$action->id}")
        ->assertStatus(200)
        ->assertDontSee('<strong>' . trans('cruds.action.fields.clauses') . '</strong>', false)
        ->assertDontSee('<strong>' . trans('cruds.action.fields.risks') . '</strong>', false);
});
