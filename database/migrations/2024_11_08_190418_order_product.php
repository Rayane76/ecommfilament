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
        Schema::create('order_product', function (Blueprint $table) {
           $table->foreignUuid('product_id')->constrained()->nullOnDelete();
           $table->foreignUuid('order_id')->constrained()->cascadeOnDelete();
           $table->string('product_title');
           $table->string('product_color')->nullable();
           $table->string('product_size')->nullable();
           $table->decimal('product_price',5,2);
           $table->integer('quantity');
           $table->timestamps();

           $table->primary(['product_id', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product');
    }
};
