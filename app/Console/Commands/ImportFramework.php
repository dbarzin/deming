<?php

namespace App\Console\Commands;

use App\Http\Controllers\MeasureImportController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportFramework extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deming:import-framework
                            {filename : the file contaning the framework}
                            {--clean : remove all other controls}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a framwork from repository';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::debug('ImportFramework - Start.');

        $fileName = $this->argument('filename');

        if (! file_exists($fileName)) {
            Log::error('ImportFramework - file does not exists');
            $this->components->error('File does not exists');
        } else {
            Log::debug('ImportFramework - Import ' . $fileName);

            // XLSX
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($fileName);

            $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
            $data = $sheet->toArray();

            // create error collection
            $errors = Collect();

            // create controller
            $importController = new MeasureImportController();

            // Import file
            if ($importController->canImportFromFile($data, $this->option('clean'), $errors)) {
                // Clear database
                if ($this->option('clean')) {
                    $importController->clean();
                    $errors->push('Database cleared');
                }

                // Improt data
                $importController->importFromFile($data, $errors);

                foreach ($errors as $error) {
                    Log::info('ImportFramework - ' . $error);
                    $this->components->info($error);
                }
            } else {
                foreach ($errors as $error) {
                    Log::error('ImportFramework - ' . $error);
                    $this->components->error($error);
                }
            }
        }

        Log::debug('ImportFramework - DONE.');
    }
}
