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
        Schema::create('log_saldos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_saldo');
            $table->bigInteger('saldo'); // jumlah transaksi
            $table->text('description')->nullable();
            $table->enum('status', ['masuk', 'keluar'])->default('masuk');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('created_name')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('updated_name')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('id_saldo')->references('id')->on('saldos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_saldos');
    }
};
