<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemStatistics extends Model
{
    protected $fillable = [
        'total_users', 'total_players', 'total_managers', 'total_coaches',
        'total_scouts', 'active_users_today', 'total_teams', 'total_matches',
        'total_contracts', 'total_messages', 'total_notifications',
        'support_tickets', 'pending_reports', 'average_response_time',
        'server_load', 'database_size', 'date',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
