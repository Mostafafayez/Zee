<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
   Schema::create('order_details', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')
          ->constrained('orders')
          ->onDelete('cascade');
    $table->foreignId('merchant_product_id')
          ->constrained('merchant_products')
          ->onDelete('cascade');
    $table->integer('quantity')->default(1);
    $table->decimal('price', 10, 2); 
    $table->text('notes')->nullable();
    $table->timestamps();
});

}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
