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
        if (Schema::hasColumn('benefit_slides', 'body') && ! Schema::hasColumn('benefit_slides', 'body_text')) {
            Schema::table('benefit_slides', function (Blueprint $table) {
                $table->renameColumn('body', 'body_text');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('benefit_slides', 'body_text') && ! Schema::hasColumn('benefit_slides', 'body')) {
            Schema::table('benefit_slides', function (Blueprint $table) {
                $table->renameColumn('body_text', 'body');
            });
        }
    }
};
