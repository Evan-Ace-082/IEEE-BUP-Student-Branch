<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leadership_profiles', function (Blueprint $table) {
            $table->string('position_label')->nullable()->after('role');
            $table->string('department')->nullable()->after('designation');
        });
    }

    public function down(): void
    {
        Schema::table('leadership_profiles', function (Blueprint $table) {
            $table->dropColumn(['position_label', 'department']);
        });
    }
};
