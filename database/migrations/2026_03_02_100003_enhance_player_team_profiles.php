<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('player_profiles', function (Blueprint $table) {
            // Temel Bilgiler
            $table->foreignId('current_club_id')->nullable()->after('current_team')->constrained('clubs')->nullOnDelete();
            $table->foreignId('primary_position_id')->nullable()->after('position')->constrained('positions')->nullOnDelete();
            $table->json('secondary_positions')->nullable()->after('primary_position_id'); // [2, 5, 7]

            // Fiziksel Özellikler
            $table->unsignedTinyInteger('preferred_foot')->nullable()->after('dominant_foot')->comment('1=Sağ, 2=Sol, 3=İkisi');
            $table->enum('body_type', ['lean', 'normal', 'stocky', 'athletic'])->nullable()->after('weight_kg');

            // Kariyer Bilgileri
            $table->foreignId('nationality_id')->nullable()->after('birth_year')->constrained('countries')->nullOnDelete();
            $table->json('second_nationalities')->nullable(); // Çifte vatandaşlık
            $table->date('date_of_birth')->nullable()->after('birth_year');
            $table->string('place_of_birth', 100)->nullable();
            $table->foreignId('youth_club_id')->nullable()->constrained('clubs')->nullOnDelete();

            // Piyasa Bilgileri
            $table->decimal('current_market_value', 12, 2)->default(0)->after('current_club_id');
            $table->decimal('highest_market_value', 12, 2)->default(0);
            $table->date('contract_expires')->nullable();
            $table->string('agent_name', 120)->nullable();

            // Sosyal Medya
            $table->string('instagram_handle', 100)->nullable();
            $table->string('twitter_handle', 100)->nullable();
            $table->unsignedInteger('social_followers')->default(0);

            // Özel Bilgiler
            $table->json('languages')->nullable(); // ["Türkçe", "İngilizce"]
            $table->string('jersey_number', 3)->nullable();
            $table->boolean('is_retired')->default(false);
            $table->date('retirement_date')->nullable();
        });

        Schema::table('team_profiles', function (Blueprint $table) {
            $table->foreignId('club_id')->nullable()->after('user_id')->constrained('clubs')->nullOnDelete();
            $table->decimal('total_market_value', 15, 2)->default(0)->after('team_name');
            $table->decimal('transfer_budget', 12, 2)->nullable();
            $table->unsignedSmallInteger('squad_size')->default(0);
            $table->string('manager_name', 100)->nullable();
            $table->string('chairman_name', 100)->nullable();
            $table->json('achievements')->nullable(); // Şampiyonluklar
            $table->json('social_media')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('team_profiles', function (Blueprint $table) {
            $table->dropForeign(['club_id']);
            $table->dropColumn([
                'club_id', 'total_market_value', 'transfer_budget',
                'squad_size', 'manager_name', 'chairman_name',
                'achievements', 'social_media'
            ]);
        });

        Schema::table('player_profiles', function (Blueprint $table) {
            $table->dropForeign(['current_club_id', 'primary_position_id', 'nationality_id', 'youth_club_id']);
            $table->dropColumn([
                'current_club_id', 'primary_position_id', 'secondary_positions',
                'preferred_foot', 'body_type', 'nationality_id', 'second_nationalities',
                'date_of_birth', 'place_of_birth', 'youth_club_id',
                'current_market_value', 'highest_market_value', 'contract_expires',
                'agent_name', 'instagram_handle', 'twitter_handle', 'social_followers',
                'languages', 'jersey_number', 'is_retired', 'retirement_date'
            ]);
        });
    }
};
