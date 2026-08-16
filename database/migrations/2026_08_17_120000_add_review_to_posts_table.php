<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->timestamp('under_review_at')->nullable()->after('content');
            $table->foreignId('reviewed_by')->nullable()->after('under_review_at')->constrained('users')->nullOnDelete();
            $table->string('review_reason')->nullable()->after('reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['under_review_at', 'review_reason']);
        });
    }
};
