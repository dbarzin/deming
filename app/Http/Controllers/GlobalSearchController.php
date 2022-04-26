<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GlobalSearchController extends Controller
{
    private $models = [
        'App\\Domain',
        'App\\Measure',
        'App\\Control',
    ];

    public function search(Request $request)
    {
        $term = $request->input('search');
        if ($term === null) 
            return redirect()->back();
        
        $searchableData = [];

        foreach ($this->models as $model) {
            $query = $model::query();
            $fields = $model::$searchable;

            foreach ($fields as $field) 
                $query->orWhere($field, 'LIKE', '%' . $term . '%');

            $results = $query->take(20)->get();

            foreach ($results as $result) {
                $parsedData           = $result->only($fields);
                $parsedData['model']  = $model;
                $parsedData['fields'] = $fields;
                $formattedFields      = [];

                foreach ($fields as $field) {
                    $formattedFields[$field] = Str::title(str_replace('_', ' ', $field));
                }

                $parsedData['fields_formated'] = $formattedFields;
                $parsedData['id'] = $result->id;
                $searchableData[] = $parsedData;
            }
        }

        return view("search",['results' => $searchableData])
            ->with("search", $term);
    }
}
