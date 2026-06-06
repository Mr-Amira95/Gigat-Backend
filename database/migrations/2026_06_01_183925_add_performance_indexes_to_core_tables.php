<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * P2-15 / PERF-08: Add missing indexes on frequently-filtered and join columns.
 *
 * Without these indexes every WHERE/JOIN on these columns performs a full table scan,
 * causing O(n) query time that grows with data volume.
 *
 * Safe to run on live data — addIndex() never modifies existing rows.
 * Each index is guarded with hasIndex() to prevent duplicate-key errors on re-run.
 */
return new class extends Migration
{
    public function up(): void
    {
        // requests table
        Schema::table('requests', function (Blueprint $table) {
            if (! $this->hasIndex('requests', 'requests_status_index')) {
                $table->index('status', 'requests_status_index');
            }
            if (! $this->hasIndex('requests', 'requests_service_id_index')) {
                $table->index('service_id', 'requests_service_id_index');
            }
            if (! $this->hasIndex('requests', 'requests_user_id_index')) {
                $table->index('user_id', 'requests_user_id_index');
            }
            if (! $this->hasIndex('requests', 'requests_plan_id_index')) {
                $table->index('plan_id', 'requests_plan_id_index');
            }
        });

        // finances table
        Schema::table('finances', function (Blueprint $table) {
            if (! $this->hasIndex('finances', 'finances_payment_status_index')) {
                $table->index('payment_status', 'finances_payment_status_index');
            }
            if (! $this->hasIndex('finances', 'finances_request_id_index')) {
                $table->index('request_id', 'finances_request_id_index');
            }
            if (! $this->hasIndex('finances', 'finances_paid_at_index')) {
                $table->index('paid_at', 'finances_paid_at_index');
            }
        });

        // services table
        Schema::table('services', function (Blueprint $table) {
            if (! $this->hasIndex('services', 'services_user_id_index')) {
                $table->index('user_id', 'services_user_id_index');
            }
        });

        // users — prefix+phone compound used in OTP lookup cache key
        Schema::table('users', function (Blueprint $table) {
            if (! $this->hasIndex('users', 'users_prefix_phone_index')) {
                $table->index(['prefix', 'phone'], 'users_prefix_phone_index');
            }
        });

        // chats — used in channel authorization and message queries
        Schema::table('chats', function (Blueprint $table) {
            if (! $this->hasIndex('chats', 'chats_user_id_one_index')) {
                $table->index('user_id_one', 'chats_user_id_one_index');
            }
            if (! $this->hasIndex('chats', 'chats_user_id_two_index')) {
                $table->index('user_id_two', 'chats_user_id_two_index');
            }
        });

        // notifications — filtered by user_id + is_read in every page load
        Schema::table('notifications', function (Blueprint $table) {
            if (! $this->hasIndex('notifications', 'notifications_user_id_is_read_index')) {
                $table->index(['user_id', 'is_read'], 'notifications_user_id_is_read_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropIndexIfExists('requests_status_index');
            $table->dropIndexIfExists('requests_service_id_index');
            $table->dropIndexIfExists('requests_user_id_index');
            $table->dropIndexIfExists('requests_plan_id_index');
        });

        Schema::table('finances', function (Blueprint $table) {
            $table->dropIndexIfExists('finances_payment_status_index');
            $table->dropIndexIfExists('finances_request_id_index');
            $table->dropIndexIfExists('finances_paid_at_index');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndexIfExists('services_user_id_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndexIfExists('users_prefix_phone_index');
        });

        Schema::table('chats', function (Blueprint $table) {
            $table->dropIndexIfExists('chats_user_id_one_index');
            $table->dropIndexIfExists('chats_user_id_two_index');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndexIfExists('notifications_user_id_is_read_index');
        });
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        return collect(DB::select("SHOW INDEX FROM `{$table}`"))
            ->pluck('Key_name')
            ->contains($indexName);
    }
};
