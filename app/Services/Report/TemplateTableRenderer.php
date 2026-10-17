<?php

namespace App\Services\Report;

use Carbon\CarbonInterface;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;

/**
 * Remplace les balises ${table:...} d'un modèle Word par des tableaux générés à partir des
 * données (voir TableTag pour la syntaxe et ReportTableRegistry pour les tables disponibles).
 *
 * Comme pour les autres tableaux du rapport (setComplexBlock), la balise doit être seule
 * dans son paragraphe : c'est tout le paragraphe qui est remplacé par le tableau.
 */
class TemplateTableRenderer
{
    /** Largeur totale des tableaux (twips), identique aux tableaux existants du rapport. */
    private const TABLE_WIDTH = 9800;

    private const HEADER_COLOR = '#FFD5CA';

    /** Colonnes de texte long : plus larges que les autres. */
    private const WIDE_TYPES = ['text', 'mediumtext', 'longtext'];

    private const WIDE_COLUMNS = ['name', 'title', 'description', 'clauses', 'owners'];

    /**
     * Balises de tableau présentes dans le modèle.
     *
     * @return array<int, TableTag>
     */
    public function tags(TemplateProcessor $templateProcessor): array
    {
        return array_values(array_map(
            fn (string $variable) => TableTag::parse($variable),
            array_filter($templateProcessor->getVariables(), fn (string $v) => TableTag::isTableTag($v))
        ));
    }

    /**
     * Vérifie les balises de tableau d'un fichier modèle.
     *
     * @return array<int, string> messages d'erreur (vide si le modèle est valide)
     */
    public function validate(string $templatePath): array
    {
        try {
            $templateProcessor = new TemplateProcessor($templatePath);
        } catch (\Throwable) {
            return [trans('report_template.errors.not_a_docx')];
        }

        $errors = [];
        foreach ($this->tags($templateProcessor) as $tag) {
            array_push($errors, ...$tag->errors);
        }

        return array_values(array_unique($errors));
    }

    /**
     * Remplace toutes les balises de tableau du modèle.
     *
     * @param  array{start?: CarbonInterface, end?: CarbonInterface, framework?: ?string}  $context
     */
    public function render(TemplateProcessor $templateProcessor, array $context): void
    {
        // Le texte des cellules est échappé par PhpWord (voir AppServiceProvider)
        $counts = $templateProcessor->getVariableCount();
        foreach ($this->tags($templateProcessor) as $tag) {
            $occurrences = $counts[$tag->variable] ?? 1;
            for ($i = 0; $i < $occurrences; $i++) {
                $element = $tag->isValid() ? $this->buildTable($tag, $context) : $this->errorTable($tag);
                $templateProcessor->setComplexBlock($tag->variable, $element);
            }
        }
    }

    /**
     * Lignes du tableau : valeurs formatées, une ligne par enregistrement.
     *
     * @return array<int, array<int, string>>
     */
    public function rows(TableTag $tag, array $context): array
    {
        $query = ReportTableRegistry::query($tag->table);

        ReportTableRegistry::applyFramework($tag->table, $query, $context['framework'] ?? null);

        if ($tag->period !== null && isset($context['start'], $context['end'])) {
            $query->whereBetween($tag->period, [
                $context['start']->format('Y-m-d'),
                $context['end']->format('Y-m-d') . ' 23:59:59',
            ]);
        }

        foreach ($tag->where as $where) {
            $value = strtolower($where['value']) === 'null' ? null : $where['value'];
            if ($value === null) {
                $where['operator'] === '=' ? $query->whereNull($where['column']) : $query->whereNotNull($where['column']);
            } else {
                $query->where($where['column'], $where['operator'], $value);
            }
        }

        foreach ($tag->order as $order) {
            $query->orderBy($order['column'], $order['direction']);
        }

        if ($tag->limit !== null) {
            $query->limit($tag->limit);
        }

        return $query->get()->map(fn ($record) => array_map(
            fn (array $column) => $this->format(ReportTableRegistry::value($tag->table, $record, $column['column'])),
            $tag->columns
        ))->all();
    }

    private function buildTable(TableTag $tag, array $context): Table
    {
        $labels = ReportTableRegistry::columns($tag->table);
        $widths = $this->widths($tag);
        $table = $this->newTable();

        $table->addRow();
        foreach ($tag->columns as $i => $column) {
            $table->addCell($widths[$i], ['bgColor' => self::HEADER_COLOR])
                ->addText($column['header'] ?? $labels[$column['column']], ['bold' => true]);
        }

        foreach ($this->rows($tag, $context) as $row) {
            $table->addRow();
            foreach ($row as $i => $value) {
                $cell = $table->addCell($widths[$i]);
                foreach (explode("\n", $value) as $line) {
                    $cell->addText($line, null, ['spaceBefore' => 0, 'spaceAfter' => 0]);
                }
            }
        }

        return $table;
    }

    /** Tableau d'une seule cellule signalant les erreurs de la balise dans le document. */
    private function errorTable(TableTag $tag): Table
    {
        $table = $this->newTable();
        $table->addRow();
        $cell = $table->addCell(self::TABLE_WIDTH);
        foreach ($tag->errors as $error) {
            $cell->addText($error, ['color' => '#CC0000']);
        }

        return $table;
    }

    private function newTable(): Table
    {
        return new Table([
            'borderSize' => 3,
            'borderColor' => 'black',
            'width' => self::TABLE_WIDTH,
            'unit' => TblWidth::TWIP,
            'layout' => \PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED,
        ]);
    }

    /**
     * Largeur de chaque colonne : les colonnes de texte long comptent triple.
     *
     * @return array<int, int>
     */
    private function widths(TableTag $tag): array
    {
        $types = [];
        foreach (\Illuminate\Support\Facades\Schema::getColumns($tag->table) as $column) {
            $types[$column['name']] = $column['type_name'];
        }

        $weights = array_map(
            fn (array $c) => (in_array($c['column'], self::WIDE_COLUMNS, true)
                || in_array($types[$c['column']] ?? '', self::WIDE_TYPES, true)) ? 3 : 1,
            $tag->columns
        );
        $total = array_sum($weights);

        return array_map(fn (int $w) => intdiv(self::TABLE_WIDTH * $w, $total), $weights);
    }

    private function format(mixed $value): string
    {
        return match (true) {
            $value === null => '',
            $value instanceof CarbonInterface => $value->format('Y-m-d'),
            is_bool($value) => $value ? '✓' : '',
            $value instanceof \BackedEnum => (string) $value->value,
            is_scalar($value) => (string) $value,
            default => json_encode($value, JSON_UNESCAPED_UNICODE) ?: '',
        };
    }
}
