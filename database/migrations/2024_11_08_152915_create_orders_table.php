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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('fname');
            $table->string('lname');
            $table->string('phone');
            $table->string('wilaya');
            $table->string('commune');
            $table->string('address');
            $table->enum('status',['Pending','Confirmed','Finished','Canceled'])->default('Pending');
            $table->decimal('total',8,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
