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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();


        $table->string('order_type');
        $table->decimal('shipping_price', 10, 2);
        $table->decimal('order_price', 10, 2);
        $table->decimal('total_price', 10, 2);

        $table->string('country')->nullable();
        $table->string('address')->nullable();

        $table->string('track_number')->unique();
        $table->enum('payment_method', ['cash', 'collected']);
        $table->enum('status', [
            'created', 'confirmed_by_admin', 'assigned',
            'on_the_way', 'delivered', 'failed', 'delayed', 'returned'
        ]);
        $table->text('failure_reason')->nullable();
        $table->text('delay_reason')->nullable();
        $table->dateTime('delay_date')->nullable();

        $table->string('receiver_name')->nullable();
        $table->string('receiver_address')->nullable();
        $table->text('note')->nullable();
        $table->timestamp('estimated_delivery')->nullable();



        $table->foreignId('courier_id')->nullable()->constrained()->onDelete('set null');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');

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
