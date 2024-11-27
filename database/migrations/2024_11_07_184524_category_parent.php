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
        Schema::create('category_parent', function(Blueprint $table) {
            $table->foreignUuid('parent_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignUuid('child_id')->constrained('categories')->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['parent_id', 'child_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_parent');
    }
};
