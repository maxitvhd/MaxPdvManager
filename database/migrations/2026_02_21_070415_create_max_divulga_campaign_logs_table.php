<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('max_divulga_campaign_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->string('status')->default('success'); // success, error
            $table->text('message')->nullable();
            $table->timestamp('executed_at')->useCurrent();
            $table->timestamps();

            $table->foreign('campaign_id')->references('id')->on('max_divulga_campaigns')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('max_divulga_campaign_logs');
    }
};
