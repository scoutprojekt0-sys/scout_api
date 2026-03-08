<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractVersion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'contract_id', 'version_number', 'changes_description',
        'content', 'modified_by', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}
