<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GlobalSearchController extends Controller
{
    private $models = [
        \App\Models\Domain::class,
        \App\Models\Measure::class,
        \App\Models\Control::class,
    ];

    public function search(Request $request)
    {
        // Not for API
        abort_if(Auth::User()->isAPI(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $term = $request->input('search');
        if ($term === null) {
            return redirect()->back();
        }

        $searchableData = [];

        foreach ($this->models as $model) {
            // Auditee only search on controls
            if (Auth::User()->isAuditee() && $model !== \App\Models\Control::class) {
                continue;
            }

            $query = $model::query();
            $fields = $model::$searchable;

            // Auditee only search on assigned controls
            if (Auth::User()->isAuditee()) {
                $userId = Auth::id();
                $query = $query->where(function($q) use ($userId) {
                    // Contrôles assignés directement à l'utilisateur
                    $q->whereExists(function($subQ) use ($userId) {
                        $subQ->selectRaw(1)
                            ->from('control_user')
                            ->whereColumn('control_user.control_id', 'controls.id')
                            ->where('control_user.user_id', $userId);
                    })
                        // OU contrôles assignés via un groupe d'utilisateurs
                        ->orWhereExists(function($subQ) use ($userId) {
                            $subQ->selectRaw(1)
                                ->from('control_user_group')
                                ->join('user_user_group', 'user_user_group.user_group_id', '=', 'control_user_group.user_group_id')
                                ->whereColumn('control_user_group.control_id', 'controls.id')
                                ->where('user_user_group.user_id', $userId);
                        });
                });
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
