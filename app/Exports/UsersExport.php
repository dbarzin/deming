<?php
namespace App\Exports;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromQuery, WithMapping, WithHeadings, WithStyles, WithColumnWidths
{
    public function headings(): array
    {
        return [
            trans('cruds.user.fields.login'),
            trans('cruds.user.fields.name'),
            trans('cruds.user.fields.title'),
            trans('cruds.user.fields.role'),
            trans('cruds.user.fields.email'),
            trans('cruds.user.fields.groups'),
            trans('cruds.user.fields.language'),
            trans('cruds.user.fields.controls'),
        ];
    }

    public function styles(Worksheet $_sheet): array
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true],
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => 'top',
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // Login
            'B' => 30,  // Name
            'C' => 20,  // Title
            'D' => 20,  // Role
            'E' => 30,  // email
            'F' => 50,  // Groups
            'G' => 10,  // language
            'H' => 50,  // Controls
        ];
    }

    public function map($user): array
    {
        return [
            [
                $user->login,
                $user->name,
                $user->title,
                $user->role,
                $user->email,
                $user->groups()->implode('name', ', '),
                $user->language,
                $user->controls->implode('name', ', ')
            ],
        ];
    }

    public function query(): Builder
    {
        return User::orderBy('login');
    }
}
