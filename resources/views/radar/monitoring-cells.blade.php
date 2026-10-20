{{-- Common statistic cells: Live/active · Overdue · Not yet overdue · Pending validation · Overdue rate [· Max days overdue] --}}
<td class="text-end">{{ $row['live'] }}</td>
<td class="text-end" @if ($row['overdue'] > 0) style="background-color:#D94F45;color:{{ contrast_color('#D94F45') }}" @endif>{{ $row['overdue'] }}</td>
<td class="text-end">{{ $row['not_yet'] }}</td>
<td class="text-end">{{ $row['pending'] }}</td>
<td class="text-end" data-order="{{ $row['rate'] }}" @if ($row['rate'] > 0) style="background-color:#E09B1A;color:{{ contrast_color('#E09B1A') }}" @endif>{{ \App\Services\MonitoringService::formatPercent($row['rate']) }}</td>
@if ($withMaxDays ?? true)
<td class="text-end">{{ $row['max_days'] }}</td>
@endif
