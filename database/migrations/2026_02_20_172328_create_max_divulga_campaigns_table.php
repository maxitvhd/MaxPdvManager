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
        Schema::create('max_divulga_campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable(); // Se houver multitenancy com tenant id
            $table->string('name');
            $table->string('type')->nullable(); // venda_direta, varejo, atacado
            $table->json('channels')->nullable(); // whatsapp, instagram, facebook
            $table->string('schedule_type')->default('unique'); // unique, daily, weekly
            $table->json('product_selection_rule')->nullable(); // rule para buscar produtos (best_sellers, category, ids)
            $table->json('discount_rules')->nullable(); // regras de desconto aplicadas
            $table->unsignedBigInteger('theme_id')->nullable();
            $table->string('persona')->nullable(); // tipo de linguagem: agressivo, emocional, etc
            $table->string('format')->nullable(); // image, text, audio
            $table->string('status')->default('active'); // active, paused, finished
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->timestamps();

            // Chaves estrangeiras se necessário
            // $table->foreign('theme_id')->references('id')->on('max_divulga_themes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('max_divulga_campaigns');
    }
};
