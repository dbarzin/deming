<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property bool $file_exists
 * @property int  $link_count
 * @property bool $hash_valid
 */
class Document extends Model
{

    public function control()
    {
        return $this->belongsTo(Control::class, 'control_id');
    }
}
