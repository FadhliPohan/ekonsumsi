<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('qty_available')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('updated_by')->nullable();
            $table->string('updated_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('food_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_food');
            $table->string('type'); // price_change, stock_in, stock_out
            $table->integer('qty')->default(0);
            $table->decimal('price_before', 15, 2)->default(0);
            $table->decimal('price_after', 15, 2)->default(0);
            $table->integer('qty_before')->default(0);
            $table->integer('qty_after')->default(0);
            $table->text('description')->nullable();
            $table->string('created_by')->nullable();
            $table->string('created_name')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_food')->references('id')->on('foods')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_logs');
        Schema::dropIfExists('foods');
    }
};
