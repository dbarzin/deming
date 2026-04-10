<?php

namespace App\Http\Controllers;

use App\Models\RiskScoringConfig;
use App\Services\RiskScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Configuration du moteur de scoring des risques.
 * Pattern URL calqué sur Deming (/risk/scoring/store, /risk/scoring/{id}/save, etc.)
 */
class RiskScoringConfigController extends Controller
{
    public function __construct(private readonly RiskScoringService $scoringService)
    {
    }

    private function checkAdmin(): void
    {
        if (Auth::user()->role !== 1) {
            abort(403);
        }
    }

    public function index(): View
    {
        $this->checkAdmin();

        $configs  = RiskScoringConfig::query()->orderByDesc('is_active')->orderBy('name')->get();
        $formulas = $this->scoringService->availableFormulas();

        return view('risks.scoring.index', compact('configs', 'formulas'));
    }

    public function create(): View
    {
        $this->checkAdmin();

        $formulas = $this->scoringService->availableFormulas();
        $config   = new RiskScoringConfig([
            'formula'            => 'probability_x_impact',
            'probability_levels' => [
                ['value' => 1, 'label' => 'Rare',          'description' => ''],
                ['value' => 2, 'label' => 'Peu probable',  'description' => ''],
                ['value' => 3, 'label' => 'Possible',      'description' => ''],
                ['value' => 4, 'label' => 'Probable',      'description' => ''],
                ['value' => 5, 'label' => 'Très probable', 'description' => ''],
            ],
            'impact_levels' => [
                ['value' => 1, 'label' => 'Négligeable', 'description' => ''],
                ['value' => 2, 'label' => 'Faible',      'description' => ''],
                ['value' => 3, 'label' => 'Modéré',      'description' => ''],
                ['value' => 4, 'label' => 'Élevé',       'description' => ''],
                ['value' => 5, 'label' => 'Critique',    'description' => ''],
            ],
            'risk_thresholds' => [
                ['level' => 'low',      'label' => 'Faible',   'max' => 4,    'color' => '#27ae60'],
                ['level' => 'medium',   'label' => 'Moyen',    'max' => 9,    'color' => '#f39c12'],
                ['level' => 'high',     'label' => 'Élevé',    'max' => 16,   'color' => '#e74c3c'],
                ['level' => 'critical', 'label' => 'Critique', 'max' => null, 'color' => '#c0392b'],
            ],
        ]);

        // Variables PHP nécessaires dans la vue form
        $probLevels  = $config->probability_levels ?? [];
        $impLevels   = $config->impact_levels      ?? [];
        $expLevels   = $config->exposure_levels    ?? [];
        $vulnLevels  = $config->vulnerability_levels ?? [];
        $thresholds  = $config->risk_thresholds ?? [];

        return view('risks.scoring.form', compact('config', 'formulas', 'probLevels', 'impLevels', 'expLevels', 'vulnLevels', 'thresholds'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->checkAdmin();

        $validated = $this->validateConfig($request);
        $validated['is_active'] = false;

        RiskScoringConfig::create($validated);

        return redirect('/risk/scoring')
            ->with('success', __('Configuration créée. Activez-la pour l\'appliquer.'));
    }

    public function edit(int $id): View
    {
        $this->checkAdmin();

        $config   = RiskScoringConfig::findOrFail($id);
        $formulas = $this->scoringService->availableFormulas();

        $probLevels  = $config->probability_levels   ?? [];
        $impLevels   = $config->impact_levels        ?? [];
        $expLevels   = $config->exposure_levels      ?? [];
        $vulnLevels  = $config->vulnerability_levels ?? [];
        $thresholds  = $config->risk_thresholds      ?? [];

        return view('risks.scoring.form', compact('config', 'formulas', 'probLevels', 'impLevels', 'expLevels', 'vulnLevels', 'thresholds'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->checkAdmin();

        $config    = RiskScoringConfig::findOrFail($id);
        $validated = $this->validateConfig($request);
        $config->update($validated);

        if ($config->is_active) {
            RiskScoringConfig::clearCache();
        }

        return redirect('/risk/scoring')
            ->with('success', __('Configuration mise à jour.'));
    }

    public function activate(int $id): RedirectResponse
    {
        $this->checkAdmin();

        $config = RiskScoringConfig::findOrFail($id);
        $config->activate();

        return redirect('/risk/scoring')
            ->with('messages', [__('Configuration "' . $config->name . '" activée.')]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkAdmin();

        $config = RiskScoringConfig::findOrFail($id);

        if ($config->is_active) {
            return back()->with('errors', [__('Impossible de supprimer la configuration active.')]);
        }

        $config->delete();

        return redirect('/risk/scoring')
            ->with('messages', [__('Configuration supprimée.')]);
    }

    private function validateConfig(Request $request): array
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'formula' => ['required', 'in:' . implode(',', array_keys(RiskScoringService::FORMULAS))],

            'probability_levels'              => ['nullable', 'array'],
            'probability_levels.*.value'      => ['required_with:probability_levels', 'integer'],
            'probability_levels.*.label'      => ['required_with:probability_levels', 'string', 'max:100'],
            'probability_levels.*.description'=> ['nullable', 'string', 'max:255'],

            'impact_levels'              => ['required', 'array', 'min:2'],
            'impact_levels.*.value'      => ['required', 'integer', 'min:1'],
            'impact_levels.*.label'      => ['required', 'string', 'max:100'],
            'impact_levels.*.description'=> ['nullable', 'string', 'max:255'],

            'exposure_levels'              => ['nullable', 'array'],
            'exposure_levels.*.value'      => ['nullable', 'integer', 'min:0'],
            'exposure_levels.*.label'      => ['nullable', 'string', 'max:100'],
            'exposure_levels.*.description'=> ['nullable', 'string', 'max:255'],

            'vulnerability_levels'              => ['nullable', 'array'],
            'vulnerability_levels.*.value'      => ['nullable', 'integer', 'min:1'],
            'vulnerability_levels.*.label'      => ['nullable', 'string', 'max:100'],
            'vulnerability_levels.*.description'=> ['nullable', 'string', 'max:255'],

            'risk_thresholds'        => ['required', 'array', 'min:2'],
            'risk_thresholds.*.level'=> ['required', 'string'],
            'risk_thresholds.*.label'=> ['required', 'string', 'max:100'],
            'risk_thresholds.*.max'  => ['nullable', 'integer', 'min:1'],
            'risk_thresholds.*.color'=> ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $needsExposure = RiskScoringService::FORMULAS[$data['formula']]['requires_exposure'] ?? false;
        if (! $needsExposure) {
            $data['probability_levels']   = $data['probability_levels'] ?? [];
            $data['exposure_levels']      = null;
            $data['vulnerability_levels'] = null;
        }

        // Le dernier seuil n'a pas de borne supérieure
        $last = count($data['risk_thresholds']) - 1;
        $data['risk_thresholds'][$last]['max'] = null;

        return $data;
    }

// -------------------------------------------------------------------------
// Helpers couleurs : migration legacy (noms de classes MetroUI) → hex
// -------------------------------------------------------------------------

/*
    private const COLOR_MAP = [
        'success'   => '#27ae60',
        'warning'   => '#f39c12',
        'danger'    => '#e74c3c',
        'alert'     => '#c0392b',
        'info'      => '#2980b9',
        'secondary' => '#7f8c8d',
    ];
*/
}