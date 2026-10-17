<?php

use App\Models\Measure;
use App\Models\User;
use App\Models\UserGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    Carbon::setTestNow('2026-10-17 10:00:00');
});

afterEach(function () {
    Carbon::setTestNow();
});

// --- Access

test('guest is redirected to login', function () {
    $this->get('/radar/monitoring')->assertRedirect('/login');
});

test('admin, user and auditor can access the monitoring page', function (string $state) {
    $user = User::factory()->{$state}()->create();
    $this->actingAs($user)->get('/radar/monitoring')
        ->assertStatus(200)
        ->assertViewIs('radar.monitoring');
})->with(['admin', 'user', 'auditor']);

test('auditee and api users cannot access the monitoring page', function (string $state) {
    $user = User::factory()->{$state}()->create();
    $this->actingAs($user)->get('/radar/monitoring')->assertStatus(403);
})->with(['auditee', 'apiUser']);

test('disabled users cannot access the monitoring page', function () {
    $user = User::factory()->create(['role' => User::ROLE_DISABLED]);
    $this->actingAs($user)->get('/radar/monitoring')->assertStatus(403);
});

test('the route is named radar.monitoring', function () {
    expect(route('radar.monitoring', [], false))->toBe('/radar/monitoring');
});

test('auditor sees the monitoring menu entry, auditee does not', function () {
    $auditor = User::factory()->auditor()->create();
    $this->actingAs($auditor)->get('/radar/monitoring')
        ->assertSee('href="/radar/monitoring" class="active"', false);

    $auditee = User::factory()->auditee()->create();
    $this->actingAs($auditee)->get('/')->assertDontSee('/radar/monitoring', false);
});

test('the menu entry is translated', function (string $language) {
    $user = User::factory()->admin()->create(['language' => $language]);
    $this->actingAs($user)->get('/radar/monitoring')
        ->assertSee('<span class="menu-text">Monitoring</span>', false);
})->with(['fr', 'en', 'de']);

// --- Rendering

test('renders the computed values', function () {
    $security = UserGroup::factory()->security()->create(['name' => 'SecOps']);
    Measure::factory()->create(['status' => 0, 'plan_date' => '2026-10-07'])->groups()->attach($security->id);
    Measure::factory()->create(['status' => 0, 'plan_date' => '2026-12-01']);
    Measure::factory()->create(['status' => 2, 'plan_date' => '2026-09-01', 'realisation_date' => '2026-09-02']);

    $admin = User::factory()->admin()->create(['language' => 'en']);
    $this->actingAs($admin)->get('/radar/monitoring')
        ->assertStatus(200)
        ->assertViewHas('total', 3)
        ->assertViewHas('live', 2)
        ->assertViewHas('overdue', 1)
        ->assertSee('<div class="fs-2 fw-bold" id="tile-rate">50.0%</div>', false)
        ->assertSee('Security: 1 of 1 live / active measurements are overdue (100.0%).');
});

test('an empty database renders with zero rates', function () {
    $admin = User::factory()->admin()->create(['language' => 'en']);
    $this->actingAs($admin)->get('/radar/monitoring')
        ->assertStatus(200)
        ->assertViewHas('overdue_rate', 0.0)
        ->assertSee('<div class="fs-2 fw-bold" id="tile-rate">0.0%</div>', false);
});

test('a disabled user with a live measure is shown with a badge', function () {
    $disabled = User::factory()->create(['name' => 'Dormant User', 'role' => User::ROLE_DISABLED]);
    Measure::factory()->create(['status' => 0])->users()->attach($disabled->id);
    User::factory()->create(['name' => 'Gone User', 'role' => User::ROLE_DISABLED]);

    $admin = User::factory()->admin()->create(['language' => 'fr']);
    $this->actingAs($admin)->get('/radar/monitoring')
        ->assertSee('Dormant User')
        ->assertSee('Désactivé')
        ->assertDontSee('Gone User');
});

// --- Scope

test('group view off restricts every block to the user measures', function () {
    $user = User::factory()->user()->create(['name' => 'Scoped User']);
    $other = User::factory()->user()->create(['name' => 'Other User']);
    $group = UserGroup::factory()->create(['name' => 'Team']);
    $group->users()->attach($user->id);

    Measure::factory()->create(['status' => 0, 'plan_date' => '2026-10-01'])->users()->attach($user->id);
    Measure::factory()->create(['status' => 0, 'plan_date' => '2026-12-01'])->groups()->attach($group->id);
    Measure::factory()->create(['status' => 0, 'plan_date' => '2026-10-01'])->users()->attach($other->id);
    Measure::factory()->create(['status' => 0, 'plan_date' => '2026-10-01']);

    // Default: all data
    $this->actingAs($user)->get('/radar/monitoring')
        ->assertViewHas('total', 4)
        ->assertViewHas('overdue', 3);

    // Toggle to "my data"
    $this->actingAs($user)->get('/group/toggle');

    $this->actingAs($user)->get('/radar/monitoring')
        ->assertViewHas('total', 2)
        ->assertViewHas('live', 2)
        ->assertViewHas('overdue', 1)
        ->assertViewHas('ownership', fn ($o) => $o['total']['live'] === 2)
        ->assertViewHas('priorities', fn ($p) => array_sum(array_column($p, 'count')) === 2)
        ->assertViewHas('combinations', fn ($c) => array_column($c, 'label') === ['Scoped User', 'Team'])
        ->assertViewHas('individuals', fn ($rows) => collect($rows)->firstWhere('name', 'Other User')['live'] === 0
            && collect($rows)->firstWhere('name', 'Scoped User')['live'] === 2)
        ->assertViewHas('groups', fn ($rows) => collect($rows)->firstWhere('name', 'Team')['live'] === 1);
});

// --- Performance

test('the number of queries does not depend on the number of measures', function () {
    $admin = User::factory()->admin()->create();

    $countQueries = function () use ($admin) {
        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->actingAs($admin)->get('/radar/monitoring')->assertStatus(200);
        $count = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $count;
    };

    $seed = function (int $n) {
        $group = UserGroup::factory()->create();
        $user = User::factory()->user()->create();
        $group->users()->attach($user->id);
        foreach (Measure::factory()->count($n)->create(['status' => 0]) as $measure) {
            $measure->users()->attach($user->id);
            $measure->groups()->attach($group->id);
        }
    };

    $seed(2);
    $small = $countQueries();
    $seed(30);
    $large = $countQueries();

    expect($large)->toBe($small);
});
