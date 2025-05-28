<?php

namespace App\Console\Commands;

use App\Models\Control;
use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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

        Log::info("Cleanup {$durationInMonths} months");

        // Compute date in the past
        $dateLimit = Carbon::now()->subMonths($durationInMonths)->toDateString();

        Log::info("Cleanup limit date {$dateLimit}");

        // Initialise counters
        $documentCount = 0;
        $controlCount = 0;

        // Get conctrols
        $oldControls = Control::whereNotNull('realisation_date')
            ->where('realisation_date', '<', $dateLimit)
            ->get();

        foreach ($oldControls as $control) {
            DB::transaction(function () use ($control) {
                // Supprimer les documents associés
                $documents = Document::where('control_id', $control->id)->get();

                foreach ($documents as $document) {
                    // Supprimer le fichier physique s'il existe
                    $filePath = storage_path('docs/' . $document->id);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }

                    // Supprimer l'enregistrement du document
                    $document->delete();

                    $documentCount++;
                }

                // Supprimer les liens dans control_measure
                DB::table('control_measure')->where('control_id', $control->id)->delete();

                // Supprimer le contrôle lui-même
                $control->delete();

                $controlCount++;
            });
        }

        Log::info("Cleanup {$documentCount} document(s).");
        Log::info("Cleanup {$controlCount} control(s).");

        Log::info('Cleanup Done.');
    }
}
