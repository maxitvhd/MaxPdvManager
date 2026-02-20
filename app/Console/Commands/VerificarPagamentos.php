<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SistemaPagamento;
use App\Models\Licenca;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class VerificarPagamentos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verificar-pagamentos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica pagamentos atrasados e inativa licenças após grace period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hoje = Carbon::now()->startOfDay();

        // 1. Marca faturas atrasadas (Data Vencimento < Hoje)
        $pagamentosPendentes = SistemaPagamento::with('licenca')
            ->where('status', 'pendente')
            ->whereDate('data_proximo_pagamento', '<', $hoje)
            ->get();

        foreach ($pagamentosPendentes as $pag) {
            $pag->status = 'atrasado';
            $pag->save();

            // Inicia o grace period na licença, se não existir
            if ($pag->licenca && !$pag->licenca->data_inativacao_grace_period) {
                $carencia = \App\Models\SistemaConfiguracao::value('carencia_dias') ?? 10;
                $pag->licenca->data_inativacao_grace_period = $hoje->copy()->addDays($carencia);
                $pag->licenca->save();
            }

            $this->info("Fatura {$pag->id} marcada como atrasada.");
            Log::info("Fatura {$pag->id} marcada como atrasada no cron.");
        }

        // 2. Verifica Inativação Automática (Grace Period explodiu)
        $licencasVencidas = Licenca::where('status', 'ativo')
            ->whereNotNull('data_inativacao_grace_period')
            ->whereDate('data_inativacao_grace_period', '<', $hoje)
            ->get();

        foreach ($licencasVencidas as $lic) {
            $lic->status = 'inativo'; // Corta o acesso
            $lic->save();
            $this->info("Licença {$lic->id} inativada por falta de pagamento.");
            Log::info("Licença {$lic->id} inativada por falta de pagamento (Grace period excedido).");
        }

        $this->info('Verificação de pagamentos concluída com sucesso.');
    }
}
