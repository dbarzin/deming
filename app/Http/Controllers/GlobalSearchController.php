<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GlobalSearchController extends Controller
{
    private $models = [
        'App\\Models\\Domain',
        'App\\Models\\Measure',
        'App\\Models\\Control',
    ];

    public function search(Request $request)
    {
        // Not for API
        abort_if(Auth::User()->role === 4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $term = $request->input('search');
        if ($term === null) {
            return redirect()->back();
        }

        $searchableData = [];

        foreach ($this->models as $model) {
            // user only search on controls
            if (
                (Auth::User()->role === 5) && ($model != 'App\\Models\\Control')
            ) {
                continue;
            }

            $query = $model::query();
            $fields = $model::$searchable;

            // user only search on assigned controls
            if (Auth::User()->role === 5) {
                $query = $query
                    ->join('control_user', 'controls.id', '=', 'control_user.control_id')
                    ->where('control_user.user_id', '=', Auth::User()->id);
            }

            $query = $query->where(function ($subQuery) use ($fields, $term) {
                foreach ($fields as $field) {
                    if ($field === reset($fields)) {
                        $subQuery = $subQuery->where($field, 'LIKE', '%' . $term . '%');
                    } else {
                        $subQuery = $subQuery->orWhere($field, 'LIKE', '%' . $term . '%');
                    }
                }
            });

            // newest first
            $query->orderBy('id', 'desc');
            $results = $query->take(20)->get();

            foreach ($results as $result) {
                $parsedData = $result->only($fields);
                $parsedData['model'] = $model;
                $parsedData['fields'] = $fields;
                $formattedFields = [];

                foreach ($fields as $field) {
                    $formattedFields[$field] = Str::title(str_replace('_', ' ', $field));
                }

                $parsedData['fields_formated'] = $formattedFields;
                $parsedData['id'] = $result->id;
                $searchableData[] = $parsedData;
            }
        }

        return view('search', ['results' => $searchableData])
            ->with('search', $term);
    }
}
