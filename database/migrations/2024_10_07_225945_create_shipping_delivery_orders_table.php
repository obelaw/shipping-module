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
        Schema::create($this->prefix . 'shipping_delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->morphs('shippable', 'shippable_index');
            $table->foreignId('account_id')->nullable()->constrained($this->prefix . 'shipping_courier_accounts')->cascadeOnDelete();
            $table->decimal('cod_amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->prefix . 'shipping_delivery_orders');
    }
};
