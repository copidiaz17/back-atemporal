<?php

use App\Enums\UserType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('type')->after('type_id')->nullable();
        });

        DB::table('users')
            ->where('type_id', 1)
            ->update(['type' => UserType::Admin]);

        DB::table('users')
            ->where('type_id', 2)
            ->update(['type' => UserType::Client]);

        Schema::dropIfExists('user_type');

        Schema::table('users', function (Blueprint $table) {
            $table->string('type')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
