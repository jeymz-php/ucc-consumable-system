<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemStatus extends Model
{
    protected $table    = 'system_status';
    protected $fillable = ['system', 'status', 'reason', 'resolved_by', 'changed_by', 'changed_at'];
    protected $dates    = ['changed_at'];

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Default to 'cs' in the CS app — so any call without a parameter checks CS
    public static function current(string $system = 'cs')
    {
        return static::where('system', $system)->latest()->first();
    }

    public static function isDown(string $system = 'cs'): bool
    {
        return static::current($system)?->status === 'down';
    }
}