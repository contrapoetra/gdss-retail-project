<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsensusLog extends Model
{
    use HasFactory;
    protected $fillable = ['period_id', 'triggered_by'];

    public function period() { return $this->belongsTo(Period::class); }
    public function user() { return $this->belongsTo(User::class, 'triggered_by'); }
    public function results() { return $this->hasMany(BordaResult::class); }
}