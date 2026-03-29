<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('application_apis', function (Blueprint $table) {
            $table->json('scopes')->nullable()->after('memo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_apis', function (Blueprint $table) {
            $table->dropColumn('scopes');
        });
    }
};

