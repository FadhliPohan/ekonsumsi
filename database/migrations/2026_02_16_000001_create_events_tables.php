<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->unsignedBigInteger('id_departemen')->nullable();
            $table->string('name_departemen')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=Open,2=Approved VP,3=OnProcess,4=Approved VP Umum,5=Reject,6=Close by Umum,7=Close by User');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('location')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('id_user_created');
            $table->string('name_user_created');
            $table->text('description')->nullable();
            $table->text('reject_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('consumtions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_event');
            $table->unsignedBigInteger('id_food')->nullable();
            $table->string('food_name')->nullable();
            $table->unsignedBigInteger('id_departemen')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->string('user_name')->nullable();
            $table->integer('qty')->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('status')->nullable()->comment('masuk/keluar');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_event')->references('id')->on('events')->onDelete('cascade');
        });

        Schema::create('pesertas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_event');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name');
            $table->tinyInteger('status')->default(1)->comment('1=hadir, 2=tidak hadir');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_event')->references('id')->on('events')->onDelete('cascade');
        });

        Schema::create('event_status_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('id_event');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->tinyInteger('status_from')->nullable();
            $table->tinyInteger('status_to');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_event')->references('id')->on('events')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_status_logs');
        Schema::dropIfExists('pesertas');
        Schema::dropIfExists('consumtions');
        Schema::dropIfExists('events');
    }
};
