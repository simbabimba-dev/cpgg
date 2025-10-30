<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add indexes to users table for frequently queried columns
        Schema::table('users', function (Blueprint $table) {
            if (!$this->hasIndex('users', 'users_pterodactyl_id_index')) {
                $table->index('pterodactyl_id', 'users_pterodactyl_id_index');
            }
            if (!$this->hasIndex('users', 'users_suspended_index')) {
                $table->index('suspended', 'users_suspended_index');
            }
            if (!$this->hasIndex('users', 'users_referral_code_index')) {
                $table->index('referral_code', 'users_referral_code_index');
            }
            if (!$this->hasIndex('users', 'users_last_seen_index')) {
                $table->index('last_seen', 'users_last_seen_index');
            }
        });

        // Add indexes to servers table for frequently queried columns
        Schema::table('servers', function (Blueprint $table) {
            if (!$this->hasIndex('servers', 'servers_pterodactyl_id_index')) {
                $table->index('pterodactyl_id', 'servers_pterodactyl_id_index');
            }
            if (!$this->hasIndex('servers', 'servers_user_id_index')) {
                $table->index('user_id', 'servers_user_id_index');
            }
            if (!$this->hasIndex('servers', 'servers_product_id_index')) {
                $table->index('product_id', 'servers_product_id_index');
            }
            if (!$this->hasIndex('servers', 'servers_suspended_canceled_index')) {
                $table->index(['suspended', 'canceled'], 'servers_suspended_canceled_index');
            }
            if (!$this->hasIndex('servers', 'servers_last_billed_index')) {
                $table->index('last_billed', 'servers_last_billed_index');
            }
        });

        // Add indexes to payments table for frequently queried columns
        Schema::table('payments', function (Blueprint $table) {
            if (!$this->hasIndex('payments', 'payments_user_id_index')) {
                $table->index('user_id', 'payments_user_id_index');
            }
            if (!$this->hasIndex('payments', 'payments_status_created_index')) {
                $table->index(['status', 'created_at'], 'payments_status_created_index');
            }
            if (!$this->hasIndex('payments', 'payments_currency_code_index')) {
                $table->index('currency_code', 'payments_currency_code_index');
            }
        });

        // Add indexes to tickets table for frequently queried columns
        Schema::table('tickets', function (Blueprint $table) {
            if (!$this->hasIndex('tickets', 'tickets_user_id_index')) {
                $table->index('user_id', 'tickets_user_id_index');
            }
            if (!$this->hasIndex('tickets', 'tickets_status_index')) {
                $table->index('status', 'tickets_status_index');
            }
            if (!$this->hasIndex('tickets', 'tickets_updated_at_index')) {
                $table->index('updated_at', 'tickets_updated_at_index');
            }
        });

        // Add indexes to user_referrals table for frequently queried columns
        Schema::table('user_referrals', function (Blueprint $table) {
            if (!$this->hasIndex('user_referrals', 'user_referrals_referral_id_index')) {
                $table->index('referral_id', 'user_referrals_referral_id_index');
            }
            if (!$this->hasIndex('user_referrals', 'user_referrals_registered_user_id_index')) {
                $table->index('registered_user_id', 'user_referrals_registered_user_id_index');
            }
        });
    }

    /**
     * Helper method to check if an index exists
     */
    private function hasIndex($table, $indexName)
    {
        $connection = Schema::getConnection();
        $doctrineSchemaManager = $connection->getDoctrineSchemaManager();
        $doctrineTable = $doctrineSchemaManager->listTableDetails($table);
        
        return $doctrineTable->hasIndex($indexName);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if ($this->hasIndex('users', 'users_pterodactyl_id_index')) {
                $table->dropIndex('users_pterodactyl_id_index');
            }
            if ($this->hasIndex('users', 'users_suspended_index')) {
                $table->dropIndex('users_suspended_index');
            }
            if ($this->hasIndex('users', 'users_referral_code_index')) {
                $table->dropIndex('users_referral_code_index');
            }
            if ($this->hasIndex('users', 'users_last_seen_index')) {
                $table->dropIndex('users_last_seen_index');
            }
        });

        Schema::table('servers', function (Blueprint $table) {
            if ($this->hasIndex('servers', 'servers_pterodactyl_id_index')) {
                $table->dropIndex('servers_pterodactyl_id_index');
            }
            if ($this->hasIndex('servers', 'servers_user_id_index')) {
                $table->dropIndex('servers_user_id_index');
            }
            if ($this->hasIndex('servers', 'servers_product_id_index')) {
                $table->dropIndex('servers_product_id_index');
            }
            if ($this->hasIndex('servers', 'servers_suspended_canceled_index')) {
                $table->dropIndex('servers_suspended_canceled_index');
            }
            if ($this->hasIndex('servers', 'servers_last_billed_index')) {
                $table->dropIndex('servers_last_billed_index');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if ($this->hasIndex('payments', 'payments_user_id_index')) {
                $table->dropIndex('payments_user_id_index');
            }
            if ($this->hasIndex('payments', 'payments_status_created_index')) {
                $table->dropIndex('payments_status_created_index');
            }
            if ($this->hasIndex('payments', 'payments_currency_code_index')) {
                $table->dropIndex('payments_currency_code_index');
            }
        });

        Schema::table('tickets', function (Blueprint $table) {
            if ($this->hasIndex('tickets', 'tickets_user_id_index')) {
                $table->dropIndex('tickets_user_id_index');
            }
            if ($this->hasIndex('tickets', 'tickets_status_index')) {
                $table->dropIndex('tickets_status_index');
            }
            if ($this->hasIndex('tickets', 'tickets_updated_at_index')) {
                $table->dropIndex('tickets_updated_at_index');
            }
        });

        Schema::table('user_referrals', function (Blueprint $table) {
            if ($this->hasIndex('user_referrals', 'user_referrals_referral_id_index')) {
                $table->dropIndex('user_referrals_referral_id_index');
            }
            if ($this->hasIndex('user_referrals', 'user_referrals_registered_user_id_index')) {
                $table->dropIndex('user_referrals_registered_user_id_index');
            }
        });
    }
};
