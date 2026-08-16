<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('is_showcased')->default(false)->after('content');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->date('answers_publish_on')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('is_showcased');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('answers_publish_on');
        });
    }
};
