<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Obelaw\Shipping\Enums\DocumentState;
use Twist\Base\BaseMigration;

return new class extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->prefix . 'shipping_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained($this->prefix . 'shipping_delivery_orders')->cascadeOnDelete();
            $table->smallInteger('state')->index()->default(DocumentState::PREPARE);
            $table->string('document_number');
            $table->string('document_file')->nullable();
            $table->string('courier_status')->nullable();
            $table->timestamp('cancel_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->prefix . 'shipping_delivery_order_awbs');
    }
};
