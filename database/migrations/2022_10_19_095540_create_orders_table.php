<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable('customers')->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('status')->nullable();
            $table->string('number', 32)->unique();
            $table->decimal('total_price', 12, 2)->default(0);
            $table->string('currency')->nullable();
            $table->decimal('shipping_price')->default(0);
            $table->string('shipping_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
