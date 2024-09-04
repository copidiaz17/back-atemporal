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
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('venta_id');
            $table->primary(['producto_id', 'venta_id']);
            $table->integer('venta_cantidad');
            $table->decimal('venta_precio', 10, 2);
            $table->decimal('venta_total', 10, 2);
            $table->foreign('producto_id')->references('id')->on('producto');
            $table->foreign('venta_id')->references('id')->on('venta');
            $table->timestamps();
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
