<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    protected $fillable = ['type_id', 'value', 'ts'];
    public function type()
    {
        return $this->belongsTo(MeasurementType::class, 'type_id');
    }
}
