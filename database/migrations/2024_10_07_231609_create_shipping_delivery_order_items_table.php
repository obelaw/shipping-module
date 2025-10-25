<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Twist\Base\BaseMigration;

return new class extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->prefix . 'shipping_delivery_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained($this->prefix . 'shipping_delivery_orders')->cascadeOnDelete();
            $table->morphs('itemable', 'itemable_index');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->prefix . 'shipping_delivery_order_items');
    }
};
