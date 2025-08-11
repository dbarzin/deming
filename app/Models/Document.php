<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 class Document extends Model
{
    public function control()
    {
        return $this->belongsTo(Control::class, 'control_id');
    }
}
