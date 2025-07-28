<?php

namespace App\Console\Commands;

use App\Models\Control;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Cleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deming:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old controls and documents';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('Cleanup started');
        // Get duration in months
        $durationInMonths = config('deming.cleanup-duration');

        if ($durationInMonths > 0) {
            Log::info("Cleanup {$durationInMonths} months");

            // Compute date in the past
            $dateLimit = Carbon::now()->subMonths($durationInMonths)->toDateString();

            Log::info("Cleanup limit date {$dateLimit}");

            $result = Control::cleanup($dateLimit, false);

            Log::info("Cleanup {$result['logCount']} log(s).");
            Log::info("Cleanup {$result['documentCount']} document(s).");
            Log::info("Cleanup {$result['controlCount']} control(s).");
        }

        Log::info('Cleanup Done.');
    }
}
