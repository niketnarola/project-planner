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
        Schema::table('projects', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('status');

            $table->boolean('priority')->nullable()
                ->after('status')
                ->comment('1 - Highest, 2 - High, 3 - Normal, 4 - Low, 5 - Lowest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('completed_at');
            $table->dropColumn('priority');
        });
    }
};
