<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('follows', function (Blueprint $table) {
            $table->timestamp('accepted_at')->nullable()->after('following_id');
        });

        DB::table('follows')->update(['accepted_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('follows', function (Blueprint $table) {
            $table->dropColumn('accepted_at');
        });
    }
};
