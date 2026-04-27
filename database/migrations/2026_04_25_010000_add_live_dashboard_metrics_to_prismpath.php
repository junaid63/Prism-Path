<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLiveDashboardMetricsToPrismpath extends Migration
{
    public function up()
    {
        Schema::table('uc_visitors', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->after('ip_hash');
            $table->string('city')->nullable()->after('country');
            $table->boolean('is_authenticated')->default(false)->after('os');
            $table->string('user_identifier')->nullable()->after('is_authenticated');
        });

        Schema::table('uc_sessions', function (Blueprint $table) {
            $table->string('current_url', 2048)->nullable()->after('exit_page');
            $table->string('current_path')->nullable()->after('current_url')->index();
            $table->string('source')->nullable()->after('current_path')->index();
            $table->unsignedInteger('click_count')->default(0)->after('event_count');
            $table->unsignedInteger('movement_count')->default(0)->after('click_count');
            $table->unsignedSmallInteger('last_scroll_depth')->default(0)->after('movement_count');
            $table->timestamp('last_activity_at')->nullable()->after('ended_at')->index();
        });

        Schema::table('uc_page_views', function (Blueprint $table) {
            $table->unsignedSmallInteger('sequence')->default(1)->after('session_id');
        });

        Schema::create('uc_behavior_events', function (Blueprint $table) {
            $table->id();
            if (method_exists($table, 'foreignId')) {
                $table->foreignId('session_id')->constrained('uc_sessions')->cascadeOnDelete();
                $table->foreignId('page_view_id')->nullable()->constrained('uc_page_views')->nullOnDelete();
            } else {
                $table->unsignedBigInteger('session_id');
                $table->foreign('session_id')->references('id')->on('uc_sessions')->onDelete('cascade');
                $table->unsignedBigInteger('page_view_id')->nullable();
                $table->foreign('page_view_id')->references('id')->on('uc_page_views')->onDelete('set null');
            }
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

    public function down()
    {
        Schema::dropIfExists('uc_behavior_events');

        Schema::table('uc_page_views', function (Blueprint $table) {
            $table->dropColumn('sequence');
        });

        Schema::table('uc_sessions', function (Blueprint $table) {
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

        Schema::table('uc_visitors', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'city', 'is_authenticated', 'user_identifier']);
        });
    }
}

