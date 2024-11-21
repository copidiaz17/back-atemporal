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
        Schema::create('venta_detalle', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producto_id')->nullable();
            $table->unsignedBigInteger('venta_id')->nullable();
            $table->integer('venta_cantidad');
            $table->decimal('venta_precio', 10, 2);
            $table->decimal('venta_total', 10, 2);
            $table->foreign('producto_id')->references('id')->on('producto')->onDelete('cascade');
            $table->foreign('venta_id')->references('id')->on('venta')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::drop('venta_detalle');
    }
};
