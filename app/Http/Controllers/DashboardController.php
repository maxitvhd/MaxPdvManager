<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Loja;
use App\Models\LojaVenda;
use App\Models\LojaVendaItem;
use App\Models\Cliente;
use App\Models\LojaCaixaSessao;

class DashboardController extends Controller
{
    /**
     * Helper de Segurança (Mesma lógica do FuncionarioController)
     */
    private function getLojasPermitidas($user)
    {
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return Loja::orderBy('nome')->get();
        }
        return Loja::where('user_id', $user->id)
            ->orWhereHas('permissoes', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('nome')->get();
    }

    private function validarAcessoLoja($codigoAlvo)
    {
        $user = auth()->user();
        $lojas = $this->getLojasPermitidas($user);

        // Se não passou código, pega a primeira permitida
        if (!$codigoAlvo) {
            return $lojas->first();
        }

        $loja = $lojas->firstWhere('codigo', $codigoAlvo);

        if (!$loja)
            abort(403, 'Acesso não autorizado a esta loja.');

        return $loja;
    }

    /**
     * DASHBOARD PRINCIPAL (Visão Geral)
     */
    public function index(Request $request)
    {
        // Visão Global para Super Admin/Owner
        if (auth()->user()->hasRole('admin') && !$request->has('loja_codigo')) {
            $lojasPermitidas = $this->getLojasPermitidas(auth()->user());
            $loja = null;

            $lojasAtivas = \App\Models\Loja::where('status', 'ativo')
                ->orWhere('status', '1')->count();

            $pagamentosMes = \App\Models\SistemaTransacao::where('tipo', 'pagamento')
                ->whereMonth('data_transacao', Carbon::now()->month)
                ->sum('valor');

            $estornosMes = \App\Models\SistemaTransacao::whereIn('tipo', ['estorno', 'estorno_reverso'])
                ->where('valor', '<', 0)
                ->whereMonth('data_transacao', Carbon::now()->month)
                ->sum('valor'); // será negativo, podemos somar ao total se quisermos o líquido

            $totalLiquidoMes = $pagamentosMes + $estornosMes;

            $lojasVencer = \App\Models\Licenca::where('status', 'ativo')
                ->whereDate('validade', '<=', Carbon::now()->addDays(7))
                ->count();

            $historicoGlobal = \App\Models\SistemaTransacao::where('tipo', 'pagamento')
                ->where('data_transacao', '>=', Carbon::now()->subDays(15))
                ->select(DB::raw('DATE(data_transacao) as data'), DB::raw('SUM(valor) as total'))
                ->groupBy('data')
                ->orderBy('data')
                ->get();

            if ($lojasPermitidas->isEmpty()) {
                return view('dashboard.vazio', ['isVazio' => true]);
            }

            return view('dashboard.admin', compact('lojasAtivas', 'pagamentosMes', 'totalLiquidoMes', 'lojasVencer', 'historicoGlobal', 'lojasPermitidas', 'loja'));
        }

        $loja = $this->validarAcessoLoja($request->get('loja_codigo'));
        if (!$loja)
            return view('dashboard.vazio', ['isVazio' => true]);

        $hoje = Carbon::today();
        $mesAtual = Carbon::now()->startOfMonth();
        $mesPassado = Carbon::now()->subMonth()->startOfMonth();

        // --- 1. KPIs PODEROSOS ---

        // Faturamento Hoje
        $vendasHoje = LojaVenda::where('loja_id', $loja->id)->whereDate('data_hora', $hoje)->sum('total');

        // Comparativo: Mesmo dia da semana passada (Ex: Terça hoje vs Terça passada)
        $vendasSemanaPassadaDia = LojaVenda::where('loja_id', $loja->id)
            ->whereDate('data_hora', Carbon::today()->subWeek())
            ->sum('total');

        $crescimentoDiario = $vendasSemanaPassadaDia > 0
            ? (($vendasHoje - $vendasSemanaPassadaDia) / $vendasSemanaPassadaDia) * 100
            : 100;

        // Lucro Bruto Hoje (Preço - Custo)
        // Precisamos somar (quantidade * (preco_unitario - custo))
        $lucroHoje = LojaVendaItem::join('loja_vendas', 'loja_vendas_itens.loja_venda_id', '=', 'loja_vendas.id')
            ->where('loja_vendas.loja_id', $loja->id)
            ->whereDate('loja_vendas.data_hora', $hoje)
            ->sum(DB::raw('loja_vendas_itens.quantidade * (loja_vendas_itens.preco_unitario - loja_vendas_itens.custo)'));

        // Quantidade de Vendas (Tickets)
        $qtdVendasHoje = LojaVenda::where('loja_id', $loja->id)->whereDate('data_hora', $hoje)->count();

        // Ticket Médio Real
        $ticketMedio = $qtdVendasHoje > 0 ? $vendasHoje / $qtdVendasHoje : 0;

        // --- 2. GRÁFICO PRINCIPAL (Venda vs Lucro - Últimos 7 dias) ---
        $historico7Dias = LojaVenda::where('loja_id', $loja->id)
            ->where('data_hora', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->select(
                DB::raw('DATE(data_hora) as data'),
                DB::raw('SUM(total) as faturamento')
            )
            ->groupBy('data')
            ->orderBy('data')
            ->get();

        // Para o lucro, precisamos de uma query separada agrupada por dia e cruzada com itens
        $lucro7Dias = LojaVendaItem::join('loja_vendas', 'loja_vendas_itens.loja_venda_id', '=', 'loja_vendas.id')
            ->where('loja_vendas.loja_id', $loja->id)
            ->where('loja_vendas.data_hora', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->select(
                DB::raw('DATE(loja_vendas.data_hora) as data'),
                DB::raw('SUM(loja_vendas_itens.quantidade * (loja_vendas_itens.preco_unitario - loja_vendas_itens.custo)) as lucro')
            )
            ->groupBy('data')
            ->orderBy('data')
            ->get();

        // --- 3. CATEGORIAS MAIS VENDIDAS (Pie Chart) ---
        $categorias = LojaVendaItem::join('loja_vendas', 'loja_vendas_itens.loja_venda_id', '=', 'loja_vendas.id')
            ->where('loja_vendas.loja_id', $loja->id)
            ->whereMonth('loja_vendas.data_hora', Carbon::now()->month)
            ->select('loja_vendas_itens.categoria', DB::raw('SUM(loja_vendas_itens.subtotal) as total'))
            ->groupBy('loja_vendas_itens.categoria')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // --- 4. TOP PRODUTOS (Mantendo o clássico) ---
        $topProdutos = LojaVendaItem::join('loja_vendas', 'loja_vendas_itens.loja_venda_id', '=', 'loja_vendas.id')
            ->where('loja_vendas.loja_id', $loja->id)
            ->whereMonth('loja_vendas.data_hora', Carbon::now()->month)
            ->select('loja_vendas_itens.produto_nome', DB::raw('SUM(loja_vendas_itens.quantidade) as qtd'))
            ->groupBy('loja_vendas_itens.produto_nome')
            ->orderByDesc('qtd')
            ->limit(5)
            ->get();

        return view('dashboard.index', [
            'loja' => $loja,
            'lojasPermitidas' => $this->getLojasPermitidas(auth()->user()),
            'vendasHoje' => $vendasHoje,
            'crescimentoDiario' => $crescimentoDiario,
            'lucroHoje' => $lucroHoje,
            'qtdVendasHoje' => $qtdVendasHoje,
            'ticketMedio' => $ticketMedio,
            'historico7Dias' => $historico7Dias,
            'lucro7Dias' => $lucro7Dias, // Passando o lucro para o gráfico
            'categorias' => $categorias,
            'topProdutos' => $topProdutos
        ]);
    }

    /**
     * ANÁLISE OPERACIONAL (Cruzamento de Horários e Produtos)
     */
    /**
     * ANÁLISE OPERACIONAL (Cruzamento de Horários e Produtos)
     */
    public function operacional(Request $request)
    {
        $loja = $this->validarAcessoLoja($request->get('loja_codigo'));

        // 1. MAPA DE CALOR (Horários de Pico)
        $fluxoHorario = LojaVenda::where('loja_id', $loja->id)
            ->select(DB::raw('HOUR(data_hora) as hora'), DB::raw('COUNT(*) as total_vendas'))
            ->groupBy('hora')
            ->orderBy('hora')
            ->get();

        // 2. DIAS DA SEMANA MAIS MOVIMENTADOS
        $diasSemana = LojaVenda::where('loja_id', $loja->id)
            ->select(DB::raw('DAYOFWEEK(data_hora) as dia'), DB::raw('SUM(total) as total'))
            ->groupBy('dia')
            ->get();

        // 3. FORMAS DE PAGAMENTO (Pizza)
        $pagamentos = LojaVenda::where('loja_id', $loja->id)
            ->select('metodo_pagamento', DB::raw('count(*) as qtd'), DB::raw('sum(total) as valor'))
            ->groupBy('metodo_pagamento')
            ->get();

        // 4. TEMPO MÉDIO DE ATENDIMENTO
        $produtividade = LojaVenda::where('loja_id', $loja->id)
            ->select('operador', DB::raw('AVG(tempo_atendimento) as tempo_medio'), DB::raw('COUNT(*) as vendas_qtd'))
            ->groupBy('operador')
            ->get();

        // CORREÇÃO: Adicionado 'lojasPermitidas'
        return view('dashboard.operacional', [
            'loja' => $loja,
            'lojasPermitidas' => $this->getLojasPermitidas(auth()->user()), // <--- Faltava isso
            'fluxoHorario' => $fluxoHorario,
            'diasSemana' => $diasSemana,
            'pagamentos' => $pagamentos,
            'produtividade' => $produtividade
        ]);
    }

    /**
     * FINANCEIRO E CLIENTES (Inadimplência e Crédito)
     */
    public function financeiro(Request $request)
    {
        $loja = $this->validarAcessoLoja($request->get('loja_codigo'));

        // Filtro de Data (Padrão: últimos 30 dias)
        $dataInicio = Carbon::now()->subDays(30);
        $dataFim = Carbon::now();

        // 1. KPI: TOTAIS GERAIS
        $totalReceber = Cliente::where('loja_id', $loja->id)->sum('credito_usado');

        // Total Recebido (Pagamentos) este mês
        $totalRecebidoMes = DB::table('clientes_transacoes')
            ->where('loja_id', $loja->id)
            ->where('tipo', 'pagamento')
            ->whereMonth('data_hora', Carbon::now()->month)
            ->sum('valor');

        // 2. GRÁFICO: FLUXO DE CAIXA (Vendas a Prazo vs Pagamentos Recebidos)
        // Isso mostra a saúde do fiado: se está entrando mais dinheiro do que saindo
        $fluxoCaixa = DB::table('clientes_transacoes')
            ->where('loja_id', $loja->id)
            ->whereBetween('data_hora', [$dataInicio, $dataFim])
            ->select(
                DB::raw('DATE(data_hora) as data'),
                DB::raw("SUM(CASE WHEN tipo = 'debito' THEN valor ELSE 0 END) as total_vendido"),
                DB::raw("SUM(CASE WHEN tipo = 'pagamento' THEN valor ELSE 0 END) as total_pago")
            )
            ->groupBy('data')
            ->orderBy('data')
            ->get();

        // 3. RÉGUA DE COBRANÇA (Inadimplentes / Maior Risco)
        $maioresDevedores = Cliente::where('loja_id', $loja->id)
            ->where('credito_usado', '>', 0)
            ->orderByDesc('credito_usado') // Ordena do maior devedor para o menor
            ->limit(10)
            ->get();

        // 4. MELHORES PAGADORES (Baseado em transações de pagamento)
        // Busca quem pagou mais vezes ou maiores valores recentemente
        $melhoresPagadores = DB::table('clientes_transacoes')
            ->join('clientes', 'clientes_transacoes.cliente_codigo', '=', 'clientes.codigo')
            ->where('clientes_transacoes.loja_id', $loja->id)
            ->where('clientes_transacoes.tipo', 'pagamento')
            ->select(
                'clientes.nome',
                'clientes.telefone',
                DB::raw('COUNT(*) as qtd_pagamentos'),
                DB::raw('SUM(clientes_transacoes.valor) as total_pago'),
                DB::raw('MAX(clientes_transacoes.data_hora) as ultimo_pagamento')
            )
            ->groupBy('clientes.nome', 'clientes.telefone')
            ->orderByDesc('total_pago')
            ->limit(8)
            ->get();

        return view('dashboard.financeiro', [
            'loja' => $loja,
            'lojasPermitidas' => $this->getLojasPermitidas(auth()->user()),
            'totalReceber' => $totalReceber,
            'totalRecebidoMes' => $totalRecebidoMes,
            'fluxoCaixa' => $fluxoCaixa,
            'maioresDevedores' => $maioresDevedores,
            'melhoresPagadores' => $melhoresPagadores
        ]);
    }
}