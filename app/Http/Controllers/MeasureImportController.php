<?php

namespace App\Http\Controllers;

use App\Models\Control;
use App\Models\Document;
use App\Models\Domain;
use App\Models\Measure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;

class MeasureImportController extends Controller
{
    /**
     * Show Import Measure screen
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        // Only for Administrator
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $models = Storage::disk('local')->files('repository');

        // filter .xlsx files
        $models = array_filter(
            $models,
            fn ($str) => str_ends_with($str, '.xlsx')
        );

        return view('measures/import')
            ->with('models', $models);
    }


    /**
     * Download Measures
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */

    public function download(Request $request)
    {
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'model' => 'required|string',
        ]);

        $model = '/' . $request->get('model') . '.xlsx';
        $file = current(
            array_filter(
                Storage::disk('local')->files('repository'),
                fn($e) => str_contains($e, $model)
            )
        );

        if (!$file) {
            abort(404, 'Model not found');
        }

        $fileName = Storage::disk('local')->path($file);

        return response()->download(
            $fileName,
            $request->get('model') . '.xlsx',
            ['Content-Type' => 'application/octet-stream']
        );
    }

    /**
     * Import Measures
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */

    public function import(Request $request)
    {
        // Only for Administrator
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'file' => 'required_without:model|mimes:xls,xlsx',
            'model' => 'required_without:file',
        ]);

        $errors = Collect();
        $messages = Collect();

        try {
            // Get Filename
            if ($request->file()) {
                // Save temp file
                $fileName = Storage::path($request->file('file')->store());
            } else {
                // Find file from repositories
                $model = '/' . $request->get('model') . '.xlsx';
                $file = current(
                    array_filter(
                        Storage::disk('local')->files('repository'),
                        function ($e) use ($model) {
                            return str_contains($e, $model);
                        }
                    )
                );
                // Get full path
                $fileName = Storage::disk('local')->path($file);
            }

            // XLSX
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($fileName);

            $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
            $data = $sheet->toArray();

            if ($this->canImportFromFile($data, $request->has('clean'), $errors)) {
                // Clear database
                if ($request->has('clean')) {
                    $this->clean();
                    $messages->push('Database cleared');
                }
                $this->importFromFile($data, $messages);
            }
        } finally {
            if ($request->file()) {
                unlink($fileName);
            }
        }

        // Generate fake test data
        if ($request->has('test')) {
            // Call command
            Artisan::call('deming:generate-tests');
            $messages->push('Test data generated');
        }

        return back()
            ->with('errors', $errors)
            ->with('messages', $messages)
            ->with('file', $fileName);
    }

    /**
     * Check Measures from an XLSX file
     *
     * @return Bool
     */
    public function canImportFromFile(
        array $data,
        bool $clear,
        Collection $errors
    ) : Bool {
        /*
        +-------------+---------------+------+-----+---------+----------------+
        | Field       | Type          | Null | Key | Default | Extra          |
        +-------------+---------------+------+-----+---------+----------------+
      0 | framework | varchar(32)  | NO   | MUL | NULL     |                |
      1 | domain.name | varchar(32)  | NO   | MUL | NULL     |                |
      2 | domain.desc | varchar(255)  | NO   | MUL | NULL    |                |
      3 | clause      | varchar(32)   | NO   |     | NULL    |                |
      4 | name        | varchar(255)  | NO   |     | NULL    |                |
      5 | objective   | text          | YES  |     | NULL    |                |
      6 | attributes  | varchar(1024) | YES  |     | NULL    |                |
      7 | input       | text          | YES  |     | NULL    |                |
      8 | model       | text          | YES  |     | NULL    |                |
      9 | indicator   | text          | YES  |     | NULL    |                |
      10 | action_plan | text          | YES  |     | NULL    |                |
        +-------------+---------------+------+-----+---------+----------------+
        */

        // Check for errors
        $lastLine = count($data);

        for ($line = 1; $line < $lastLine; $line++) {
            if ($errors->count() > 10) {
                $errors->push('too many errors...');
                break;
            }

            if (($data[$line][1] === null)) {
                $errors->push(($line + 1) . ': framework name is empty');
                continue;
            }
            if (($data[$line][1] === null)) {
                $errors->push(($line + 1) . ': domain name is empty');
                continue;
            }
            if (($data[$line][3] === null)) {
                $errors->push(($line + 1) . ': close is empty');
                continue;
            }
            // delete line ?
            if (
                ($data[$line][3] !== null) &&
                ($data[$line][4] === null) &&
                ($data[$line][5] === null) &&
                ($data[$line][6] === null) &&
                ($data[$line][7] === null) &&
                ($data[$line][8] === null) &&
                ($data[$line][9] === null) &&
                ($data[$line][10] === null)
            ) {
                continue;
            }
            if (strlen($data[$line][0]) >= 32) {
                $errors->push(($line + 1) . ': framework name is too long');
                continue;
            }
            if (strlen($data[$line][1]) >= 32) {
                $errors->push(($line + 1) . ': domain name is too long');
                continue;
            }
            if (strlen($data[$line][2]) >= 255) {
                $errors->push(($line + 1) . ': domain description is too long');
                continue;
            }
            if (strlen($data[$line][3]) === 0) {
                $errors->push(($line + 1) . ': close name is empty');
                continue;
            }
            if (strlen($data[$line][3]) >= 32) {
                $errors->push(($line + 1) . ': close name too long');
                continue;
            }
            if (! $clear && Measure::where('clause', $data[$line][3])->exists()) {
                $errors->push(($line + 1) . ': close name not unique');
                continue;
            }
            if (strlen($data[$line][4]) === 0) {
                $errors->push(($line + 1) . ': name is empty');
                continue;
            }
            if (strlen($data[$line][4]) >= 255) {
                $errors->push(($line + 1) . ': name too long ');
                continue;
            }
            // TODO: check tags
        }

        return $errors->count() === 0;
    }

    /**
     * Import Measures from an XLSX file
     *
     * @return \Illuminate\Http\Response
     */
    public function importFromFile(
        array $data,
        Collection $messages
    ) {
        // Initialize counters
        $deleteCount = 0;
        $insertCount = 0;
        $updateCount = 0;
        $newDomainCount = 0;
        $deleteControlCount = 0;
        $deleteDocumentCount = 0;

        // Read file
        $lastLine = count($data);
        for ($line = 1; $line < $lastLine; $line++) {
            // Update domain description ?
            // delete line ?
            if (
                ($data[$line][3] !== null) &&
                ($data[$line][4] === null) &&
                ($data[$line][5] === null) &&
                ($data[$line][6] === null) &&
                ($data[$line][7] === null) &&
                ($data[$line][8] === null) &&
                ($data[$line][9] === null) &&
                ($data[$line][10] === null)
            ) {
                // delete documents
                $documents = DB::table('documents')
                    ->join('controls', 'controls.id', '=', 'documents.control_id')
                    ->join('measures', 'measures.id', '=', 'controls.measure_id')
                    ->where('measures.clause', $data[$line][3])
                    ->select('documents.id', )
                    ->get();

                foreach ($documents as $document) {
                    unlink(storage_path('docs/' . $document->id));
                    DB::table('documents')->where('id', $document->id)->delete();
                    $deleteDocumentCount++;
                }

                // Break link between controls
                Control::join('measures', 'measures.id', '=', 'controls.measure_id')
                    ->where('measures.clause', $data[$line][3])
                    ->update(['next_id' => null]);

                // Delete controls
                $controls = Control::join('measures', 'measures.id', '=', 'controls.measure_id')
                    ->where('measures.clause', $data[$line][3])
                    ->get(['controls.id']);

                Control::destroy($controls->toArray());

                $deleteControlCount += count($controls);

                // delete measure
                measure::where('clause', $data[$line][3])->delete();

                // TODO: delete empty domains

                $deleteCount++;
                continue;
            }
            // update or insert ?
            $measure = Measure::where('clause', $data[$line][3])->get()->first();

            if ($measure !== null) {
                // update or create domain
                $domain = Domain
                    ::where('framework', trim($data[$line][0]))
                        ->where('title', trim($data[$line][1]))
                        ->get()->first();
                if ($domain === null) {
                    // create domain
                    $domain = new Domain();
                    $domain->framework = trim($data[$line][0]);
                    $domain->title = trim($data[$line][1]);
                    $domain->description = trim($data[$line][2]);
                    $domain->save();

                    $newDomainCount++;
                } else {
                    $domain->description = trim($data[$line][2]);
                    $domain->update();
                }

                // update measure
                $measure->name = $data[$line][4];
                $measure->domain_id = $domain->id;
                $measure->objective = $data[$line][5];
                $measure->attributes = $data[$line][6];
                $measure->input = $data[$line][7];
                $measure->model = $data[$line][8];
                $measure->indicator = $data[$line][9];
                $measure->action_plan = $data[$line][10];
                $measure->update();

                $updateCount++;
            } else {
                // insert

                // get domain id
                $domain = Domain
                    ::where('framework', trim($data[$line][0]))
                        ->where('title', trim($data[$line][1]))
                        ->get()->first();

                if ($domain === null) {
                    // create domain
                    $domain = new Domain();
                    $domain->framework = trim($data[$line][0]);
                    $domain->title = trim($data[$line][1]);
                    $domain->description = trim($data[$line][2]);
                    $domain->save();

                    $newDomainCount++;
                } else {
                    $domain->description = trim($data[$line][2]);
                    $domain->update();
                }

                // create measure
                $measure = new Measure();

                $measure->domain_id = $domain->id;
                $measure->clause = $data[$line][3];
                $measure->name = $data[$line][4];
                $measure->objective = $data[$line][5];
                $measure->attributes = $data[$line][6];
                $measure->input = $data[$line][7];
                $measure->model = $data[$line][8];
                $measure->indicator = $data[$line][9];
                $measure->action_plan = $data[$line][10];

                $measure->save();

                $insertCount++;
            }
        }

        if ($insertCount > 0) {
            $messages->push($insertCount . ' lines inserted');
        }
        if ($updateCount > 0) {
            $messages->push($updateCount . ' lines updated');
        }
        if ($deleteCount > 0) {
            $messages->push($deleteCount . ' lines deleted');
        }
        if ($deleteControlCount > 0) {
            $messages->push($deleteControlCount . ' controls deleted');
        }
        if ($deleteDocumentCount > 0) {
            $messages->push($deleteDocumentCount . ' documents deleted');
        }
        if ($newDomainCount > 0) {
            $messages->push($newDomainCount . ' new domains created');
        }
    }

    /**
     * Truncate database
     *
     * @return \Illuminate\Http\Response
     */
    public function clean()
    {
        Schema::disableForeignKeyConstraints();

        // Delete all documents
        Document::truncate();

        // Delete all controls
        Control::truncate();

        // Delete all measures
        Measure::truncate();

        // Delete all domains
        Domain::truncate();

        Schema::enableForeignKeyConstraints();
    }
}
