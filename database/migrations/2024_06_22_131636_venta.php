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
    Schema::create('venta', function (Blueprint $table) {
        $table->id();
        $table->date('venta_fecha');
        $table->unsignedBigInteger('cliente_id')->nullable();
        $table->foreign('cliente_id')->references('id')->on('users')->onDelete('cascade');
        $table->timestamps();

        $table->softDeletes();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("producto");
    }
};
