<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrismpathTables extends Migration
{
    public function up()
    {
        Schema::create('uc_visitors', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_uuid')->unique();
            $table->string('site_id')->index();
            $table->string('ip_hash')->nullable()->index();
            $table->string('country', 2)->nullable();
            $table->string('device')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->json('traits')->nullable();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('uc_sessions', function (Blueprint $table) {
            $table->id();
            if (method_exists($table, 'foreignId')) {
                $table->foreignId('visitor_id')->constrained('uc_visitors')->cascadeOnDelete();
            } else {
                $table->unsignedBigInteger('visitor_id');
                $table->foreign('visitor_id')->references('id')->on('uc_visitors')->onDelete('cascade');
            }
            $table->string('session_uuid')->unique();
            $table->string('landing_page')->nullable();
            $table->string('exit_page')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->unsignedSmallInteger('max_scroll_depth')->default(0);
            $table->unsignedInteger('event_count')->default(0);
            $table->longText('recording_payload')->nullable();
            $table->string('compression')->default('gzbase64');
            $table->json('ai_summary')->nullable();
            $table->timestamp('started_at')->nullable()->index();
            $table->timestamp('ended_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('uc_page_views', function (Blueprint $table) {
            $table->id();
            if (method_exists($table, 'foreignId')) {
                $table->foreignId('session_id')->constrained('uc_sessions')->cascadeOnDelete();
            } else {
                $table->unsignedBigInteger('session_id');
                $table->foreign('session_id')->references('id')->on('uc_sessions')->onDelete('cascade');
            }
            $table->string('url', 2048);
            $table->string('path')->index();
            $table->string('title')->nullable();
            $table->string('referrer', 2048)->nullable();
            $table->unsignedInteger('viewport_width')->nullable();
            $table->unsignedInteger('viewport_height')->nullable();
            $table->unsignedInteger('time_on_page')->default(0);
            $table->unsignedSmallInteger('scroll_depth')->default(0);
            $table->timestamp('viewed_at')->index();
            if (method_exists($table, 'foreignId')) {
                $table->foreignId('session_id')->constrained('uc_sessions')->cascadeOnDelete();
                $table->foreignId('page_view_id')->nullable()->constrained('uc_page_views')->nullOnDelete();
            } else {
                $table->unsignedBigInteger('session_id');
                $table->foreign('session_id')->references('id')->on('uc_sessions')->onDelete('cascade');
                $table->unsignedBigInteger('page_view_id')->nullable();
                $table->foreign('page_view_id')->references('id')->on('uc_page_views')->onDelete('set null');
            }

        Schema::create('uc_click_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('uc_sessions')->cascadeOnDelete();
            $table->foreignId('page_view_id')->nullable()->constrained('uc_page_views')->nullOnDelete();
            $table->string('path')->index();
            $table->unsignedInteger('x');
            $table->unsignedInteger('y');
            $table->unsignedInteger('viewport_width')->nullable();
            $table->unsignedInteger('viewport_height')->nullable();
            $table->string('selector')->nullable();
            if (method_exists($table, 'foreignId')) {
                $table->foreignId('session_id')->nullable()->constrained('uc_sessions')->nullOnDelete();
            } else {
                $table->unsignedBigInteger('session_id')->nullable();
                $table->foreign('session_id')->references('id')->on('uc_sessions')->onDelete('set null');
            }
            $table->timestamp('clicked_at')->index();
            $table->timestamps();
        });

        Schema::create('uc_custom_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->nullable()->constrained('uc_sessions')->nullOnDelete();
            $table->string('name')->index();
            $table->string('path')->nullable()->index();
            $table->json('properties')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
        });

        Schema::create('uc_heatmap_data', function (Blueprint $table) {
            $table->id();
            $table->string('path')->index();
            $table->string('type')->index();
            $table->json('points');
            $table->json('hotspots')->nullable();
            $table->json('ai_insights')->nullable();
            $table->unsignedInteger('sample_size')->default(0);
            $table->timestamp('generated_at')->nullable()->index();
            $table->timestamps();
            $table->unique(['path', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('uc_heatmap_data');
        Schema::dropIfExists('uc_custom_events');
        Schema::dropIfExists('uc_click_events');
        Schema::dropIfExists('uc_page_views');
        Schema::dropIfExists('uc_sessions');
        Schema::dropIfExists('uc_visitors');
    }
}

