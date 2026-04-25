<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uc_visitors', function (Blueprint $table): void {
            $table->string('ip_address')->nullable()->after('ip_hash');
            $table->string('city')->nullable()->after('country');
            $table->boolean('is_authenticated')->default(false)->after('os');
            $table->string('user_identifier')->nullable()->after('is_authenticated');
        });

        Schema::table('uc_sessions', function (Blueprint $table): void {
            $table->string('current_url', 2048)->nullable()->after('exit_page');
            $table->string('current_path')->nullable()->after('current_url')->index();
            $table->string('source')->nullable()->after('current_path')->index();
            $table->unsignedInteger('click_count')->default(0)->after('event_count');
            $table->unsignedInteger('movement_count')->default(0)->after('click_count');
            $table->unsignedSmallInteger('last_scroll_depth')->default(0)->after('movement_count');
            $table->timestamp('last_activity_at')->nullable()->after('ended_at')->index();
        });

        Schema::table('uc_page_views', function (Blueprint $table): void {
            $table->unsignedSmallInteger('sequence')->default(1)->after('session_id');
        });

        Schema::create('uc_behavior_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('session_id')->constrained('uc_sessions')->cascadeOnDelete();
            $table->foreignId('page_view_id')->nullable()->constrained('uc_page_views')->nullOnDelete();
            $table->string('type')->index();
            $table->string('path')->index();
            $table->unsignedInteger('x')->nullable();
            $table->unsignedInteger('y')->nullable();
            $table->unsignedSmallInteger('scroll_depth')->nullable();
            $table->unsignedInteger('viewport_width')->nullable();
            $table->unsignedInteger('viewport_height')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedInteger('occurred_ms')->default(0);
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uc_behavior_events');

        Schema::table('uc_page_views', function (Blueprint $table): void {
            $table->dropColumn('sequence');
        });

        Schema::table('uc_sessions', function (Blueprint $table): void {
            $table->dropColumn([
                'current_url',
                'current_path',
                'source',
                'click_count',
                'movement_count',
                'last_scroll_depth',
                'last_activity_at',
            ]);
        });

        Schema::table('uc_visitors', function (Blueprint $table): void {
            $table->dropColumn(['ip_address', 'city', 'is_authenticated', 'user_identifier']);
        });
    }
};

