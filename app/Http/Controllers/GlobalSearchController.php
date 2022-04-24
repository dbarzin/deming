<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GlobalSearchController extends Controller
{
    private $models = [
        'Domain'          => 'Domaine',
        'Measure'         => 'Mesure',
        'Control'         => 'Control',
    ];

    public function search(Request $request)
    {
        $term = $request->input('search');

        \Log::Alert("search: ".$term);

        if ($term === null) 
            return redirect()->back();
        
        $searchableData = [];

        foreach ($this->models as $model => $translation) {
            $modelClass = 'App\\' . $model;
            $query      = $modelClass::query();

            $fields = $modelClass::$searchable;

            foreach ($fields as $field) {
                $query->orWhere($field, 'LIKE', '%' . $term . '%');
            }

            // \Log::Debug($query);

            $results = $query->take(10)
                ->get();

            foreach ($results as $result) {
                $parsedData           = $result->only($fields);
                $parsedData['model']  = trans($translation);
                $parsedData['fields'] = $fields;
                $formattedFields      = [];

                foreach ($fields as $field) {
                    $formattedFields[$field] = Str::title(str_replace('_', ' ', $field));
                }

                $parsedData['fields_formated'] = $formattedFields;

                $parsedData['url'] = url('/' . Str::plural(Str::snake($model, '-')) . '/' . $result->id );

                $searchableData[] = $parsedData;
            }
        }

        return view("search",['results' => $searchableData])
            ->with("search", $term);
    }
}
