<?php

use App\Models\Measure;
use App\Models\User;
use App\Models\UserGroup;
use App\Services\MonitoringService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Reference date: 2026-10-17
function monitoringToday(): Carbon
{
    return Carbon::parse('2026-10-17');
}

function monitoringMeasure(int $status, string $planDate, array $users = [], array $groups = []): Measure
{
    $measure = Measure::factory()->create([
        'status' => $status,
        'plan_date' => $planDate,
        'realisation_date' => $status === 0 ? null : '2026-10-10',
    ]);
    $measure->users()->attach(array_map(fn ($u) => $u->id, $users));
    $measure->groups()->attach(array_map(fn ($g) => $g->id, $groups));

    return $measure;
}

function findRow(array $rows, string $key, mixed $value): ?array
{
    foreach ($rows as $row) {
        if ($row[$key] === $value) {
            return $row;
        }
    }

    return null;
}

beforeEach(function () {
    $this->secGroup = UserGroup::factory()->security()->create(['name' => 'SecOps']);
    $this->bizGroup = UserGroup::factory()->create(['name' => 'Finance']);

    $this->alice = User::factory()->user()->create(['name' => 'Alice']);
    $this->bob = User::factory()->user()->create(['name' => 'Bob']);
    $this->carol = User::factory()->auditee()->create(['name' => 'Carol']);
    $this->dave = User::factory()->create(['name' => 'Dave', 'role' => User::ROLE_DISABLED]);
    $this->eve = User::factory()->create(['name' => 'Eve', 'role' => User::ROLE_DISABLED]);
    $this->api = User::factory()->apiUser()->create(['name' => 'Api']);

    $this->secGroup->users()->attach($this->alice->id);
    $this->bizGroup->users()->attach($this->bob->id);

    // Security: overdue (10 days) via a member of a security group
    $this->m1 = monitoringMeasure(0, '2026-10-07', [$this->alice]);
    // Security: due today, not overdue, via a security group
    $this->m2 = monitoringMeasure(0, '2026-10-17', [], [$this->secGroup]);
    // Non-security: proposed with a past plan date => pending, never overdue
    $this->m3 = monitoringMeasure(1, '2026-09-01', [$this->bob]);
    // Non-security: overdue (5 days)
    $this->m4 = monitoringMeasure(0, '2026-10-12', [$this->bob]);
    // Non-security: overdue (3 days) via a business group
    $this->m5 = monitoringMeasure(0, '2026-10-14', [], [$this->bizGroup]);
    // Security but done => not live
    $this->m6 = monitoringMeasure(2, '2026-09-01', [$this->alice]);
    // Non-security: unassigned, not yet due
    $this->m7 = monitoringMeasure(0, '2026-12-01');
    // Non-security: overdue (16 days), assigned to a disabled user
    $this->m8 = monitoringMeasure(0, '2026-10-01', [$this->dave]);

    $this->data = (new MonitoringService())->compute(null, monitoringToday());
});

test('computes the level 1 overview', function () {
    expect($this->data['total'])->toBe(8)
        ->and($this->data['live'])->toBe(7)
        ->and($this->data['overdue'])->toBe(4)
        ->and(round($this->data['overdue_rate'], 1))->toBe(57.1);
});

test('done measures are not live', function () {
    expect($this->data['ownership']['security']['live'])->toBe(2);
    expect(findRow($this->data['individuals'], 'name', 'Alice')['direct'])->toBe(1);
});

test('proposed measures are pending, never overdue', function () {
    $bob = findRow($this->data['combinations'], 'label', 'Bob');
    expect($bob['pending'])->toBe(1)
        ->and($bob['overdue'])->toBe(1)
        ->and($bob['live'])->toBe(2);

    expect($this->data['priorities'][2]['key'])->toBe(MonitoringService::PRIORITY_PENDING)
        ->and($this->data['priorities'][2]['count'])->toBe(1);
});

test('a measure planned today is not overdue', function () {
    $row = findRow($this->data['combinations'], 'label', 'SecOps');
    expect($row['overdue'])->toBe(0)->and($row['not_yet'])->toBe(1);
});

test('computes the ownership tables', function () {
    expect($this->data['ownership_all'])->toBe(['security' => 3, 'non_security' => 5]);

    $sec = $this->data['ownership']['security'];
    $non = $this->data['ownership']['non_security'];
    expect([$sec['live'], $sec['overdue'], $sec['not_yet'], $sec['pending'], $sec['rate']])->toBe([2, 1, 1, 0, 50.0]);
    expect([$non['live'], $non['overdue'], $non['not_yet'], $non['pending'], $non['rate']])->toBe([5, 3, 1, 1, 60.0]);
    expect($this->data['ownership']['total']['live'])->toBe(7);
});

test('classifies ownership by group type', function () {
    // Directly assigned to a member of a security group
    $viaMember = monitoringMeasure(2, '2026-01-01', [$this->alice]);
    // Directly assigned to a security group
    $viaGroup = monitoringMeasure(2, '2026-01-01', [], [$this->secGroup]);
    // Only business groups or their members
    $business = monitoringMeasure(2, '2026-01-01', [$this->bob], [$this->bizGroup]);
    // Unassigned
    $none = monitoringMeasure(2, '2026-01-01');

    $data = (new MonitoringService())->compute(null, monitoringToday());

    // 3 + 2 security, 5 + 2 non-security (done measures count at level 1 only)
    expect($data['ownership_all'])->toBe(['security' => 5, 'non_security' => 7]);
    expect($data['ownership']['security']['live'])->toBe(2);
    expect($data['ownership']['non_security']['live'])->toBe(5);
});

test('every row is partitioned into overdue, not yet overdue and pending', function () {
    $rows = array_merge(
        array_values($this->data['ownership']),
        $this->data['combinations'],
        $this->data['individuals'],
        $this->data['groups'],
    );
    foreach ($rows as $row) {
        expect($row['live'])->toBe($row['overdue'] + $row['not_yet'] + $row['pending']);
    }
});

test('priority percentages sum to 100', function () {
    $counts = array_column($this->data['priorities'], 'count');
    expect($counts)->toBe([1, 3, 1, 2]);
    expect(round(array_sum(array_column($this->data['priorities'], 'percent')), 1))->toBe(100.0);
});

test('lists individuals with direct, group and live counts', function () {
    $names = array_column($this->data['individuals'], 'name');
    // Disabled user with a live measure is listed, without one is not; API users never
    expect($names)->toContain('Dave', 'Carol')->not->toContain('Eve', 'Api');

    $dave = findRow($this->data['individuals'], 'name', 'Dave');
    expect($dave['disabled'])->toBeTrue()->and($dave['overdue'])->toBe(1);

    $alice = findRow($this->data['individuals'], 'name', 'Alice');
    expect([$alice['direct'], $alice['via_group'], $alice['live']])->toBe([1, 1, 2]);

    $bob = findRow($this->data['individuals'], 'name', 'Bob');
    expect([$bob['direct'], $bob['via_group'], $bob['live'], $bob['overdue']])->toBe([2, 1, 3, 2]);

    $carol = findRow($this->data['individuals'], 'name', 'Carol');
    expect($carol['live'])->toBe(0)->and($carol['rate'])->toBe(0.0);

    // Sorted by overdue descending
    $overdue = array_column($this->data['individuals'], 'overdue');
    expect($overdue)->toBe(collect($overdue)->sortDesc()->values()->all());
});

test('a user assigned directly and via a group counts the measure once', function () {
    monitoringMeasure(0, '2026-12-01', [$this->bob], [$this->bizGroup]);

    $data = (new MonitoringService())->compute(null, monitoringToday());
    $bob = findRow($data['individuals'], 'name', 'Bob');

    // 2 + 1 direct, 1 + 1 via group, 3 + 1 live
    expect([$bob['direct'], $bob['via_group'], $bob['live']])->toBe([3, 2, 4]);
});

test('groups identical assignments into one combination', function () {
    $labels = array_column($this->data['combinations'], 'label');
    expect(array_count_values($labels)['Bob'])->toBe(1);

    $unassigned = findRow($this->data['combinations'], 'unassigned', true);
    expect($unassigned['live'])->toBe(1);

    monitoringMeasure(0, '2026-12-01', [$this->bob, $this->alice], [$this->secGroup, $this->bizGroup]);
    $data = (new MonitoringService())->compute(null, monitoringToday());
    expect($labels = array_column($data['combinations'], 'label'))->toContain('Alice, Bob, Finance, SecOps');
});

test('computes max days overdue', function () {
    expect(findRow($this->data['combinations'], 'label', 'Dave')['max_days'])->toBe(16);
    expect(findRow($this->data['combinations'], 'label', 'Bob')['max_days'])->toBe(5);
    expect(findRow($this->data['combinations'], 'label', 'SecOps')['max_days'])->toBe(0);
    expect($this->data['ownership']['total']['max_days'])->toBe(16);
});

test('computes the group summary', function () {
    $sec = findRow($this->data['groups'], 'name', 'SecOps');
    expect([$sec['type'], $sec['members'], $sec['live'], $sec['overdue']])->toBe(['security', 1, 1, 0]);

    $biz = findRow($this->data['groups'], 'name', 'Finance');
    expect([$biz['type'], $biz['members'], $biz['live'], $biz['overdue']])->toBe(['business', 1, 1, 1]);
});

test('restricts every block to the scoped user', function () {
    // Bob: m3, m4 directly, m5 via the business group
    $data = (new MonitoringService())->compute($this->bob->id, monitoringToday());

    expect([$data['total'], $data['live'], $data['overdue']])->toBe([3, 3, 2]);
    expect($data['ownership_all'])->toBe(['security' => 0, 'non_security' => 3]);
    expect($data['ownership']['security']['live'])->toBe(0);
    expect(array_column($data['priorities'], 'count'))->toBe([0, 2, 1, 0]);
    expect(array_column($data['combinations'], 'label'))->toEqualCanonicalizing(['Bob', 'Finance']);
    expect(findRow($data['individuals'], 'name', 'Alice')['live'])->toBe(0);
    expect(array_column($data['individuals'], 'name'))->not->toContain('Dave');
    expect(findRow($data['groups'], 'name', 'SecOps')['live'])->toBe(0);
    expect(findRow($data['groups'], 'name', 'Finance')['live'])->toBe(1);
});

test('formats percentages according to the locale', function () {
    app()->setLocale('en');
    expect(MonitoringService::formatPercent(48.1234))->toBe('48.1%');
    app()->setLocale('fr');
    expect(MonitoringService::formatPercent(48.1234))->toBe("48,1\u{00A0}%");
});

test('a todo measure with a realisation date is not overdue', function () {
    // e.g. a rejected proposal: status back to 0, realisation date kept
    Measure::factory()->create(['status' => 0, 'plan_date' => '2026-01-01', 'realisation_date' => '2026-02-01']);

    $data = (new MonitoringService())->compute(null, monitoringToday());

    expect($data['live'])->toBe(8)->and($data['overdue'])->toBe(4);
    expect(findRow($data['combinations'], 'unassigned', true)['not_yet'])->toBe(2);
});

test('an empty database yields zero rates', function () {
    DB::table('measure_user')->delete();
    DB::table('measure_user_group')->delete();
    DB::table('measures')->delete();

    $data = (new MonitoringService())->compute(null, monitoringToday());

    expect([$data['total'], $data['live'], $data['overdue'], $data['overdue_rate']])->toBe([0, 0, 0, 0.0]);
    foreach ($data['ownership'] as $row) {
        expect($row['rate'])->toBe(0.0);
    }
    expect(array_column($data['priorities'], 'percent'))->toBe([0.0, 0.0, 0.0, 0.0]);
    expect($data['combinations'])->toBe([]);
});
