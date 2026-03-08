<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    protected $fillable = [
        'site_name', 'site_url', 'site_description', 'support_email',
        'mail_from', 'mail_host', 'mail_port', 'max_upload_size',
        'max_messages_per_hour', 'user_registration_limit',
        'require_email_verification', 'require_phone_verification',
        'password_min_length', 'enable_direct_messaging',
        'enable_user_registration', 'enable_social_login',
        'maintenance_mode', 'maintenance_message',
    ];

    protected function casts(): array
    {
        return [
            'require_email_verification' => 'boolean',
            'require_phone_verification' => 'boolean',
            'enable_direct_messaging' => 'boolean',
            'enable_user_registration' => 'boolean',
            'enable_social_login' => 'boolean',
            'maintenance_mode' => 'boolean',
        ];
    }
}
