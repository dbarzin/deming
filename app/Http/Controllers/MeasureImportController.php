<?php

namespace App\Http\Controllers;

use App\Models\Control;
use App\Models\Domain;
use App\Models\Measure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MeasureImportController extends Controller
{
    /**
     * Import Measures
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */

    public function import(Request $request)
    {
        // Only for Administrator
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'file' => 'required|mimes:xls,xlsx',
        ]);

        if ($request->file()) {
            $fileName = null;
            // Save temp file
            try {
                $fileName = $request->file('file')->store();

                // XLSX
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load(Storage::path($fileName));

                $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
                $data = $sheet->toArray();

                $errors = Collect();
                $deleteCount = 0;
                $insertCount = 0;
                $updateCount = 0;
                $newDomainCount = 0;
                $deleteControlCount = 0;
                $deleteDocumentCount = 0;
                /*
                +-------------+---------------+------+-----+---------+----------------+
                | Field       | Type          | Null | Key | Default | Extra          |
                +-------------+---------------+------+-----+---------+----------------+
              0 | domain.name | varchar(32)  | NO   | MUL | NULL     |                |
              1 | domain.desc | varchar(255)  | NO   | MUL | NULL    |                |
              2 | clause      | varchar(32)   | NO   |     | NULL    |                |
              3 | name        | varchar(255)  | NO   |     | NULL    |                |
              4 | objective   | text          | YES  |     | NULL    |                |
              5 | attributes  | varchar(1024) | YES  |     | NULL    |                |
              6 | input       | text          | YES  |     | NULL    |                |
              7 | model       | text          | YES  |     | NULL    |                |
              8 | indicator   | text          | YES  |     | NULL    |                |
              9 | action_plan | text          | YES  |     | NULL    |                |
                +-------------+---------------+------+-----+---------+----------------+
                */

                // Check for errors
                $lastLine = count($data);
                for ($line = 1; $line < $lastLine; $line++) {
                    if (($data[$line][0] === null)) {
                        $errors->push(($line + 1) . ': domain is empty');
                        continue;
                    }
                    if (($data[$line][2] === null)) {
                        $errors->push(($line + 1) . ': close is empty');
                        continue;
                    }
                    // delete line ?
                    if (
                        ($data[$line][2] !== null) &&
                        ($data[$line][3] === null) &&
                        ($data[$line][4] === null) &&
                        ($data[$line][5] === null) &&
                        ($data[$line][6] === null) &&
                        ($data[$line][7] === null) &&
                        ($data[$line][8] === null) &&
                        ($data[$line][9] === null)
                    ) {
                        continue;
                    }
                    if (strlen($data[$line][0]) >= 32) {
                        $errors->push(($line + 1) . ': domain name is too long');
                        continue;
                    }
                    if (strlen($data[$line][1]) >= 255) {
                        $errors->push(($line + 1) . ': domain description is too long');
                        continue;
                    }
                    if (strlen($data[$line][2]) >= 32) {
                        $errors->push(($line + 1) . ': close too long');
                        continue;
                    }
                    if (strlen($data[$line][3]) === 0) {
                        $errors->push(($line + 1) . ': name is empty');
                        continue;
                    }
                    if (strlen($data[$line][3]) >= 255) {
                        $errors->push(($line + 1) . ': name too long');
                        continue;
                    }
                    // TODO: check tags

                    if ($errors->count() > 10) {
                        $errors->push('too many errors...');
                        break;
                    }
                }

                if ($errors->isEmpty()) {
                    $lastLine = count($data);
                    for ($line = 1; $line < $lastLine; $line++) {
                        // Update domain description ?
                        // delete line ?
                        if (
                            ($data[$line][2] !== null) &&
                            ($data[$line][3] === null) &&
                            ($data[$line][4] === null) &&
                            ($data[$line][5] === null) &&
                            ($data[$line][6] === null) &&
                            ($data[$line][7] === null) &&
                            ($data[$line][8] === null) &&
                            ($data[$line][9] === null)
                        ) {
                            // delete documents
                            $documents = DB::table('documents')
                                ->join('controls', 'controls.id', '=', 'documents.control_id')
                                ->join('measures', 'measures.id', '=', 'controls.measure_id')
                                ->where('measures.clause', $data[$line][2])
                                ->select('documents.id', )
                                ->get();

                            foreach ($documents as $document) {
                                unlink(storage_path('docs/' . $document->id));
                                DB::table('documents')->where('id', $document->id)->delete();
                                $deleteDocumentCount++;
                            }

                            // Break link between controls
                            Control::join('measures', 'measures.id', '=', 'controls.measure_id')
                                ->where('measures.clause', $data[$line][2])
                                ->update(['next_id' => null]);

                            // Delete controls
                            $controls = Control::join('measures', 'measures.id', '=', 'controls.measure_id')
                                ->where('measures.clause', $data[$line][2])
                                ->get(['controls.id']);

                            Control::destroy($controls->toArray());

                            $deleteControlCount += count($controls);

                            // delete measure
                            measure::where('clause', $data[$line][2])->delete();

                            // TODO: delete empty domains

                            $deleteCount++;
                            continue;
                        }
                        // update or insert ?
                        $measure = Measure::where('clause', $data[$line][2])->get()->first();

                        if ($measure !== null) {

                            // update or create domain
                            $domain = Domain::where('title', trim($data[$line][0]))->get()->first();
                            if ($domain==null) {
                                // create domain
                                $domain = new Domain();
                                $domain->title = trim($data[$line][0]);
                                $domain->description = trim($data[$line][1]);
                                $domain->save();

                                $newDomainCount++;
                            }
                            else {
                                $domain->description = $data[$line][1];
                                $domain->update();
                            }

                            // update measure
                            $measure->name = $data[$line][3];
                            $measure->domain_id = $domain->id;
                            $measure->objective = $data[$line][4];
                            $measure->attributes = $data[$line][5];
                            $measure->input = $data[$line][6];
                            $measure->model = $data[$line][7];
                            $measure->indicator = $data[$line][8];
                            $measure->action_plan = $data[$line][9];
                            $measure->update();

                            // TODO : update last control

                            $updateCount++;
                        } else {
                            // insert

                            // get domain id
                            $domain = Domain::where('title', trim($data[$line][0]))->get()->first();

                            if ($domain === null) {
                                // create domain
                                $domain = new Domain();
                                $domain->title = trim($data[$line][0]);
                                $domain->description = trim($data[$line][1]);
                                $domain->save();

                                $newDomainCount++;
                            } else {
                                $domain->description = trim($data[$line][1]);
                                $domain->update();
                            }

                            // create measure
                            $measure = new Measure();

                            $measure->domain_id = $domain->id;
                            $measure->clause = $data[$line][2];
                            $measure->name = $data[$line][3];
                            $measure->objective = $data[$line][4];
                            $measure->attributes = $data[$line][5];
                            $measure->input = $data[$line][6];
                            $measure->model = $data[$line][7];
                            $measure->indicator = $data[$line][8];
                            $measure->action_plan = $data[$line][9];

                            $measure->save();

                            $insertCount++;
                        }
                    }
                }
                if ($insertCount > 0) {
                    $errors->push($insertCount . ' lines inserted');
                }
                if ($updateCount > 0) {
                    $errors->push($updateCount . ' lines updated');
                }
                if ($deleteCount > 0) {
                    $errors->push($deleteCount . ' lines deleted');
                }
                if ($deleteControlCount > 0) {
                    $errors->push($deleteControlCount . ' controls deleted');
                }
                if ($deleteDocumentCount > 0) {
                    $errors->push($deleteDocumentCount . ' documents deleted');
                }
                if ($newDomainCount > 0) {
                    $errors->push($newDomainCount . ' new domains created');
                }
            } finally {
                unlink(Storage::path($fileName));
            }

            return back()
                ->with('errors', $errors)
                ->with('file', $fileName);
        }

        return redirect('/import-export');
    }
}
