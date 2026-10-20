<?php

namespace App\Services\Report;

/**
 * Balise de tableau d'un modèle de rapport, de la forme :
 *
 *   ${table:<table>:<colonne>[=<Entête>],...[|<option>...]}
 *
 * Options :
 *   period:<colonne date>        lignes dont la date est comprise dans la période du rapport
 *   where:<colonne>=<valeur>     filtre d'égalité (where:<colonne>!=<valeur> pour l'inverse)
 *   order:<colonne>              tri croissant (order:-<colonne> pour un tri décroissant)
 *   limit:<n>                    nombre maximal de lignes
 *
 * Exemple : ${table:actions:reference=Réf,name,owners,due_date|where:status=0|order:due_date}
 */
class TableTag
{
    public const PREFIX = 'table:';

    /** @var array<int, array{column: string, header: ?string}> */
    public array $columns = [];

    public ?string $period = null;

    /** @var array<int, array{column: string, operator: string, value: string}> */
    public array $where = [];

    /** @var array<int, array{column: string, direction: string}> */
    public array $order = [];

    public ?int $limit = null;

    /** @var array<int, string> Erreurs de syntaxe ou de colonnes inconnues */
    public array $errors = [];

    private function __construct(public readonly string $variable, public string $table = '')
    {
    }

    public static function isTableTag(string $variable): bool
    {
        return str_starts_with(trim($variable), self::PREFIX);
    }

    public static function parse(string $variable): self
    {
        $tag = new self($variable);
        $body = substr(trim($variable), strlen(self::PREFIX));

        $parts = array_map('trim', explode('|', $body));
        $definition = array_shift($parts);

        if (! str_contains($definition, ':')) {
            $tag->errors[] = trans('report_template.errors.syntax', ['tag' => $variable]);

            return $tag;
        }

        [$table, $columnList] = array_map('trim', explode(':', $definition, 2));
        $tag->table = $table;

        if (! ReportTableRegistry::has($table)) {
            $tag->errors[] = trans('report_template.errors.unknown_table', ['table' => $table]);

            return $tag;
        }

        $available = ReportTableRegistry::columns($table);
        $real = ReportTableRegistry::realColumns($table);

        foreach (array_filter(array_map('trim', explode(',', $columnList)), 'strlen') as $item) {
            [$column, $header] = array_pad(array_map('trim', explode('=', $item, 2)), 2, null);
            if (! array_key_exists($column, $available)) {
                $tag->errors[] = trans('report_template.errors.unknown_column', ['table' => $table, 'column' => $column]);

                continue;
            }
            $tag->columns[] = ['column' => $column, 'header' => $header !== '' ? $header : null];
        }

        if ($tag->columns === [] && $tag->errors === []) {
            $tag->errors[] = trans('report_template.errors.no_column', ['tag' => $variable]);
        }

        // Les options ne portent que sur des colonnes réelles (elles filtrent la requête SQL)
        $checkReal = function (string $column) use ($tag, $table, $real): bool {
            if (! in_array($column, $real, true)) {
                $tag->errors[] = trans('report_template.errors.unknown_column', ['table' => $table, 'column' => $column]);

                return false;
            }

            return true;
        };

        foreach ($parts as $option) {
            if ($option === '') {
                continue;
            }
            [$name, $argument] = array_pad(array_map('trim', explode(':', $option, 2)), 2, '');

            switch ($name) {
                case 'period':
                    if ($checkReal($argument)) {
                        $tag->period = $argument;
                    }
                    break;
                case 'where':
                    if (! preg_match('/^([\w]+)\s*(!=|=)\s*(.*)$/u', $argument, $m)) {
                        $tag->errors[] = trans('report_template.errors.syntax', ['tag' => $option]);
                        break;
                    }
                    if ($checkReal($m[1])) {
                        $tag->where[] = ['column' => $m[1], 'operator' => $m[2] === '!=' ? '<>' : '=', 'value' => trim($m[3])];
                    }
                    break;
                case 'order':
                    $direction = str_starts_with($argument, '-') ? 'desc' : 'asc';
                    $column = ltrim($argument, '-');
                    if ($checkReal($column)) {
                        $tag->order[] = ['column' => $column, 'direction' => $direction];
                    }
                    break;
                case 'limit':
                    if (ctype_digit($argument) && (int) $argument > 0) {
                        $tag->limit = (int) $argument;
                    } else {
                        $tag->errors[] = trans('report_template.errors.syntax', ['tag' => $option]);
                    }
                    break;
                default:
                    $tag->errors[] = trans('report_template.errors.unknown_option', ['option' => $name]);
            }
        }

        return $tag;
    }

    public function isValid(): bool
    {
        return $this->errors === [];
    }
}
