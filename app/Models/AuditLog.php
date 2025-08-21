<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    // Desativa os campos updated_at e created_at automáticos
    public $timestamps = false;

    protected $fillable = [
        'timestamp', 'user_id', 'user_email', 'action', 'target_type', 'target_id', 'details', 'ip_address'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}