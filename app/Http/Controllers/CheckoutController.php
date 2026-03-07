<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use App\Models\Licenca;
use App\Models\Loja;
use App\Models\User;
use App\Models\Produtos;
use App\Models\ProdutoFull;
use App\Models\LojaCancelamentoKey;
use App\Models\Mensagens;
use App\Models\Cliente;
use App\Models\ClienteTransacao; 
use App\Models\LojaVenda;
use App\Models\LojaVendaItem;
use App\Models\LojaMovimentacao;
use App\Models\LojaCaixaSessao;
use App\Models\LojaCancelamento;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use App\Models\Produto;
use Illuminate\Support\Facades\Auth;
use ZipArchive;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Checkout::with('licenca');

        if (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            $query->whereHas('licenca', function ($q) use ($user) {
                // Filtra para exibir apenas os checkouts cuja licença seja do próprio usuário ou esteja vinculada a uma loja dele
                $q->where('user_id', $user->id)
                  ->orWhereHas('loja', function ($qLoja) use ($user) {
                      $qLoja->where('user_id', $user->id);
                  });
            });
        }

        if ($request->has('licenca')) {
            $query->whereHas('licenca', function ($q) use ($request) {
                $q->where('codigo', $request->licenca);
            });
        }

        $checkouts = $query->get();
        return view('checkouts.index', compact('checkouts'));
    }

    public function create()
    {
        $user = Auth::user();
        $query = Licenca::where('status', 'ativa');

        if (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            $query->where('user_id', $user->id);
        }

        $licencas = $query->get();
        return view('checkouts.create', compact('licencas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'licenca_id' => 'required|exists:licencas,id',
            'descricao' => 'required',
        ]);

        $user = Auth::user();
        $licenca = Licenca::find($request->licenca_id);

        if (!$licenca || $licenca->status !== 'ativa') {
            return back()->withErrors(['licenca_id' => 'Licença inválida ou inativa.']);
        }

        if (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            if ($licenca->user_id !== $user->id) {
                return back()->withErrors(['licenca_id' => 'Você não tem permissão para usar esta licença.']);
            }
        }

        $codigo = Str::random(30);

        Checkout::create([
            'licenca_id' => $licenca->id,
            'codigo' => $codigo,
            'descricao' => $request->descricao,
            'ip' => $request->ip(),
            'sistema_operacional' => php_uname(),
            'hardware' => $request->header('User-Agent'),
        ]);

        return redirect()->route('checkouts.index')->with('success', 'Máquina conectada com sucesso!');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $checkout = Checkout::findOrFail($id);

        if (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            if ($checkout->licenca->user_id !== $user->id) {
                abort(403, 'Acesso não autorizado.');
            }
            $licencas = Licenca::where('status', 'ativa')->where('user_id', $user->id)->get();
        } else {
            $licencas = Licenca::where('status', 'ativa')->get();
        }

        return view('checkouts.edit', compact('checkout', 'licencas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'licenca_id' => 'required|exists:licencas,id',
            'descricao' => 'required',
        ]);

        $checkout = Checkout::findOrFail($id);
        $checkout->update($request->all());

        return redirect()->route('checkouts.index')->with('success', 'Máquina atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $checkout = Checkout::findOrFail($id);
        $checkout->delete();

        return redirect()->route('checkouts.index')->with('success', 'Máquina excluída com sucesso!');
    }

    public function toggleStatus($id)
    {
        $checkout = Checkout::findOrFail($id);
        $licenca = $checkout->licenca;

        if ($checkout->status === 'ativo') {
            $checkout->update(['status' => 'inativo', 'status_manual' => true]);
            return redirect()->back()->with('success', 'O dispositivo foi desativado com sucesso.');
        } else {
            if (!$licenca || !$licenca->isValid()) {
                return redirect()->back()->with('error', 'Não foi possível ativar. A licença está inativa ou vencida.');
            }

            $ativos = Checkout::where('licenca_id', $licenca->id)->where('status', 'ativo')->count();
            if ($ativos >= $licenca->limite_dispositivos) {
                return redirect()->back()->with('error', "Limite da Licença excedido! Seu plano atual suporta no máximo {$licenca->limite_dispositivos} dispositivo(s). Desative algum primeiro.");
            }

            $checkout->update(['status' => 'ativo', 'status_manual' => false]);
            return redirect()->back()->with('success', 'Dispositivo ativado! Ele já pode se conectar ao PDV.');
        }
    }

    public function licenca(Request $request)
    {
        $key = $request->header('Authorization');
        $key = str_replace('Bearer ', '', $key);

        Log::info('--- TENTATIVA DE ATIVAÇÃO DE PDV ---');
        Log::info('Key recebida: ' . $key);

        if (!$key) {
            Log::warning('Tentativa falhou: Chave de licença não fornecida.');
            return response()->json(['error' => 'Chave de licença não fornecida'], 400);
        }

        $licenca = Licenca::where('key', $key)->first();

        if (!$licenca) {
            Log::warning('Tentativa falhou: Chave de licença informada não existe no servidor Mestre.');
            return response()->json(['error' => 'Licença Inválida ou Inexistente! Verifique sua Chave Key.'], 403);
        }

        $dadosMaquina = $request->all();
        $licencaValid = $licenca && $licenca->isValid();

        $checkout_existente = Checkout::where('licenca_id', $licenca ? $licenca->id : 0)
            ->where('mac', $dadosMaquina['mac'] ?? null)
            ->first();

        $qntAtivos = $licenca ? Checkout::where('licenca_id', $licenca->id)->where('status', 'ativo')->count() : 0;
        $statusAtual = $checkout_existente ? $checkout_existente->status : 'inativo';
        $novoStatus = 'inativo';

        if ($licencaValid) {
            if ($statusAtual === 'ativo') {
                $novoStatus = 'ativo';
            } else {
                $foiBanidoManualmente = $checkout_existente && $checkout_existente->status_manual;

                if ($foiBanidoManualmente) {
                    $novoStatus = 'inativo';
                } else {
                    if ($qntAtivos < $licenca->limite_dispositivos) {
                        $novoStatus = 'ativo';
                    }
                }
            }
        }

        if (!$checkout_existente) {
            $checkout_existente = Checkout::create([
                'licenca_id' => $licenca->id,
                'codigo' => $dadosMaquina['codigo'] ?? null,
                'descricao' => 'PDV ' . ($dadosMaquina['hostname'] ?? 'Novo'),
                'sistema_operacional' => $dadosMaquina['sistema_operacional'] ?? null,
                'versao_sistema' => $dadosMaquina['versao_sistema'] ?? null,
                'arquitetura' => $dadosMaquina['arquitetura'] ?? null,
                'hostname' => $dadosMaquina['hostname'] ?? null,
                'ip' => $dadosMaquina['ip'] ?? null,
                'mac' => $dadosMaquina['mac'] ?? null,
                'status' => $novoStatus,
            ]);
        } else {
            $checkout_existente->update(['status' => $novoStatus, 'ip' => $dadosMaquina['ip'] ?? $checkout_existente->ip]);
        }

        if (!$licencaValid) {
            return response()->json(['error' => 'Licença inválida, inativa ou com pagamentos pendentes. Dispositivo registrado e aguardando regularização.'], 403);
        }

        if ($novoStatus === 'inativo') {
            return response()->json(['error' => 'Limite de dispositivos atingido para este plano. Gerencie seus terminais na plataforma Web.'], 403);
        }

        return response()->json([
            'success' => 'Conexão bem-sucedida',
            'checkout' => $checkout_existente,
            'validade' => $licenca->validade,
            'status' => $licenca->status,
        ]);
    }

    public function dados(Request $request)
    {
        $codigo = $request->input('codigo');
        $mac = $request->input('mac');

        if (!$codigo || !$mac) {
            return response()->json(['error' => 'Código e MAC são obrigatórios'], 400);
        }

        $checkout = Checkout::where('codigo', $codigo)
            ->where('mac', $mac)
            ->where('status', 'ativo')
            ->first();

        if (!$checkout) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $loja = Loja::where('id', $checkout->licenca->loja_id)->first();

        if (!$loja) {
            return response()->json(['error' => 'Loja não encontrada'], 404);
        }

        $user = User::find($loja->user_id);
        $user_codigo = $user ? $user->codigo : null;

        return response()->json([
            'success' => 'Dados da loja atualizados com sucesso',
            'loja' => $loja,
            'user_codigo' => $user_codigo,
        ], 200);
    }

    public function produtos(Request $request)
    {
        $codigo = $request->input('codigo');
        $mac = $request->input('mac');

        if (!$codigo || !$mac) {
            return response()->json(['error' => 'Código e MAC são obrigatórios'], 400);
        }

        $checkout = Checkout::where('codigo', $codigo)
            ->where('mac', $mac)
            ->where('status', 'ativo')
            ->first();

        if (!$checkout) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $loja = Loja::where('id', $checkout->licenca->loja_id)->first();

        if (!$loja) {
            return response()->json(['error' => 'Loja não encontrada'], 404);
        }

        $user_codigo = User::find($loja->user_id)->codigo;

        $produtos = Produto::where('loja_id', $loja->id)
            ->with('lotes')
            ->get();

        $userStoragePath = "public/lojas/{$loja->codigo}";
        Storage::makeDirectory($userStoragePath);

        $oldZipPath = storage_path("app/{$userStoragePath}/{$loja->codigo}.zip");
        if (file_exists($oldZipPath)) {
            unlink($oldZipPath);
        }

        $oldTokenPath = storage_path("app/{$userStoragePath}/{$loja->codigo}_token.txt");
        if (file_exists($oldTokenPath)) {
            unlink($oldTokenPath);
        }

        $zipFileName = "{$loja->codigo}.zip";
        $zipPath = storage_path("app/{$userStoragePath}/{$zipFileName}");
        $imagesDir = storage_path("app/public/lojas/{$loja->codigo}/produtos");

        if (!is_dir($imagesDir)) {
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                return response()->json(['error' => 'Falha ao criar arquivo ZIP'], 500);
            }
            $zip->addEmptyDir('empty');
            $zip->close();
        } else {
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                return response()->json(['error' => 'Falha ao criar arquivo ZIP'], 500);
            }

            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($imagesDir),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($imagesDir) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }

            if (!$zip->close()) {
                return response()->json(['error' => 'Falha ao fechar o arquivo ZIP'], 500);
            }
        }

        if (!file_exists($zipPath)) {
            return response()->json(['error' => 'Falha ao criar arquivo ZIP: arquivo não encontrado'], 500);
        }

        $token = Str::random(32);
        $tokenPath = storage_path("app/{$userStoragePath}/{$loja->codigo}_token.txt");
        file_put_contents($tokenPath, $token);

        $zipUrl = url("/api/download-zip/{$loja->codigo}?token={$token}");

        return response()->json([
            'success' => 'Produtos recuperados com sucesso',
            'produtos' => $produtos,
            'user_codigo' => $loja->codigo,
            'imagens_zip_url' => $zipUrl,
            'token' => $token,
        ], 200);
    }

    public function downloadZip($user_codigo, Request $request)
    {
        $token = $request->query('token');

        $tokenPath = storage_path("app/public/lojas/{$user_codigo}/{$user_codigo}_token.txt");
        if (!file_exists($tokenPath)) {
            return response()->json(['error' => 'Token inválido ou expirado'], 403);
        }

        $storedToken = file_get_contents($tokenPath);
        if ($token !== $storedToken) {
            return response()->json(['error' => 'Token inválido'], 403);
        }

        $codigo = $request->header('X-Codigo');
        $mac = $request->header('X-Mac');

        if (!$codigo || !$mac) {
            return response()->json(['error' => 'Código e MAC são obrigatórios'], 400);
        }

        $checkout = Checkout::where('codigo', $codigo)
            ->where('mac', $mac)
            ->where('status', 'ativo')
            ->first();

        if (!$checkout) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $zipPath = storage_path("app/public/lojas/{$user_codigo}/{$user_codigo}.zip");
        if (!file_exists($zipPath)) {
            return response()->json(['error' => 'Arquivo ZIP não encontrado'], 404);
        }

        unlink($tokenPath);

        return response()->download($zipPath, "{$user_codigo}.zip", [
            'Content-Type' => 'application/zip',
        ]);
    }

    public function funcionarios(Request $request)
    {
        $codigo = $request->input('codigo');
        $mac = $request->input('mac');

        if (!$codigo || !$mac) {
            return response()->json(['error' => 'Código e MAC são obrigatórios'], 400);
        }

        $checkout = Checkout::where('codigo', $codigo)
            ->where('mac', $mac)
            ->where('status', 'ativo')
            ->first();

        if (!$checkout) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $loja = Loja::where('id', $checkout->licenca->loja_id)->first();
        $user_codigo = User::find($loja->user_id)->codigo;

        $funcionarios = User::whereHas('permissoes', function ($query) use ($loja) {
            $query->where('loja_id', $loja->id);
        })->get(['id', 'name', 'usuario', 'email', 'imagem', 'password', 'codigo', 'endereco', 'bairro', 'cidade', 'estado', 'cep', 'avatar', 'phone', 'about_me', 'funcao', 'acesso']);

        return response()->json([
            'success' => 'Funcionários recuperados com sucesso',
            'funcionarios' => $funcionarios,
            'user_codigo' => $user_codigo,
        ]);
    }

    public function cancelamentoChaves(Request $request)
    {
        $codigo = $request->input('codigo');
        $mac = $request->input('mac');

        if (!$codigo || !$mac) {
            return response()->json(['error' => 'Código e MAC são obrigatórios'], 400);
        }

        $checkout = Checkout::where('codigo', $codigo)
            ->where('mac', $mac)
            ->where('status', 'ativo')
            ->first();

        if (!$checkout) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $loja = Loja::where('id', $checkout->licenca->loja_id)->first();

        if (!$loja) {
            return response()->json(['error' => 'Loja não encontrada'], 404);
        }

        $chaves = LojaCancelamentoKey::where('loja_id', $loja->id)
            ->with('user')
            ->get()
            ->map(function ($chave) {
                return [
                    'chave' => $chave->chave,
                    'funcionario_nome' => $chave->user->name,
                    'funcionario_codigo' => $chave->user->codigo,
                ];
            });

        return response()->json([
            'success' => 'Chaves de cancelamento recuperadas com sucesso',
            'loja_id' => $loja->id,
            'chaves' => $chaves,
        ], 200);
    }

    // ==============================================================
    // INÍCIO: FUNÇÕES DE SINCRONIZAÇÃO DE PRODUTOS E IMAGENS (NOVO)
    // ==============================================================

    public function storeProdutosUnified(Request $request)
    {
        $codigo = $request->input('codigo');
        $mac = $request->input('mac');
        $loja_codigo = $request->input('loja_codigo');

        if (!$codigo || !$mac) {
            return response()->json(['error' => 'Código e MAC são obrigatórios'], 400);
        }

        $checkout = Checkout::where('codigo', $codigo)->where('mac', $mac)->where('status', 'ativo')->first();
        if (!$checkout) return response()->json(['error' => 'Acesso negado'], 403);

        $loja = Loja::where('codigo', $loja_codigo)->first();
        if (!$loja) return response()->json(['error' => 'Loja não encontrada'], 404);

        $productsJson = $request->input('products');
        $products = json_decode($productsJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) return response()->json(['error' => 'JSON inválido'], 400);

        $response = ['produtos_full' => [], 'produtos' => []];
        $dono_id = $loja->user_id ?? ($loja->users()->first()->id ?? null);

        foreach ($products as $product) {
            $barcode = $product['codigo_barra'];
            
            // Pega o nome da imagem enviado pelo Extrator
            $imagem_nome = $product['imagem'] ?? "{$barcode}.jpg";

            $produto_full = ProdutoFull::firstOrCreate(
                ['codigo_barra' => $barcode],
                [
                    'nome' => $product['nome'] ?? 'Produto Sem Nome',
                    'categoria' => $product['categoria'] ?? 'Geral',
                    'descricao' => $product['descricao'] ?? '',
                    'peso' => $product['peso'] ?? 0,
                    'imagem' => $imagem_nome
                ]
            );

            $produto_data = $product;
            if ($dono_id) {
                $produto_data['user_id'] = $dono_id;
            }
            $produto_data['loja_id'] = $loja->id; 
            $produto_data['produto_full_id'] = $produto_full->id;
            $produto_data['imagem'] = $imagem_nome;

            $produto_loja = Produto::updateOrCreate(
                [
                    'loja_id' => $loja->id,
                    'produto_full_id' => $produto_full->id
                ],
                $produto_data
            );

            $response['produtos_full'][$barcode] = $produto_full->id;
            $response['produtos'][$barcode] = $produto_loja->id;
        }

        Log::info("Sincronização de Lote de Produtos concluída para a loja: {$loja_codigo}");
        return response()->json(['success' => 'Sincronização concluída', 'data' => $response], 201);
    }

    public function uploadImagensZip(Request $request)
    {
        $codigo = $request->input('codigo');
        $mac = $request->input('mac');
        $loja_codigo = $request->input('loja_codigo');

        if (!$codigo || !$mac) return response()->json(['error' => 'Código e MAC são obrigatórios'], 400);

        $checkout = Checkout::where('codigo', $codigo)->where('mac', $mac)->where('status', 'ativo')->first();
        if (!$checkout) return response()->json(['error' => 'Acesso negado'], 403);

        $loja = Loja::where('codigo', $loja_codigo)->first();
        if (!$loja) return response()->json(['error' => 'Loja não encontrada'], 404);

        if ($request->hasFile('imagens_zip')) {
            Log::info('[1] Upload ZIP - Recebido arquivo imagens_zip.');
            
            $zipFile = $request->file('imagens_zip');
            $zipPath = $zipFile->storeAs('temp', $zipFile->getClientOriginalName());
            $tempDir = storage_path('app/temp/' . uniqid());
            
            Log::info("[2] Upload ZIP - Arquivo salvo em: {$zipPath}. Preparando extração...");

            $zip = new \ZipArchive;
            if ($zip->open(storage_path('app/' . $zipPath)) === TRUE) {
                
                if (!file_exists($tempDir)) {
                    mkdir($tempDir, 0777, true);
                }
                
                $zip->extractTo($tempDir);
                $zip->close();
                Log::info('[3] Upload ZIP - ZIP descompactado com sucesso.');

                $extractedImages = glob($tempDir . '/*.{jpg,png}', GLOB_BRACE);
                
                if ($extractedImages) {
                    Log::info('[4] Upload ZIP - ' . count($extractedImages) . ' imagens extraídas.');
                    
                    foreach ($extractedImages as $imagePath) {
                        $filename = basename($imagePath);
                        $barcode = pathinfo($filename, PATHINFO_FILENAME);
                        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
        
                        $fullFilename = "{$barcode}.{$extension}";
                        
                        Storage::disk('public')->put("produtos_full/{$fullFilename}", file_get_contents($imagePath));
                        Storage::disk('public')->makeDirectory("lojas/{$loja->codigo}/produtos");
                        Storage::disk('public')->put("lojas/{$loja->codigo}/produtos/{$fullFilename}", file_get_contents($imagePath));
                    }
                    Log::info('[5] Upload ZIP - Cópias de imagens concluídas com sucesso.');
                } else {
                    Log::warning('[4] Upload ZIP - Nenhuma imagem .jpg ou .png encontrada dentro do ZIP.');
                }

                if (file_exists($tempDir)) {
                    array_map('unlink', glob("$tempDir/*.*"));
                    rmdir($tempDir);
                }
            } else {
                Log::error('[ERRO] Upload ZIP - Falha ao abrir o arquivo ZIP.');
                return response()->json(['error' => 'Falha ao processar o arquivo ZIP'], 500);
            }

            if (Storage::exists($zipPath)) {
                Storage::delete($zipPath);
            }

            return response()->json(['success' => 'Imagens processadas com sucesso'], 201);
        }

        Log::warning('[0] Upload ZIP - Endpoint acessado, mas nenhum arquivo imagens_zip foi enviado na requisição.');
        return response()->json(['error' => 'Nenhum arquivo enviado'], 400);
    }

    private function getExistingImagePath($folder, $barcode)
    {
        if (Storage::disk('public')->exists("{$folder}/{$barcode}.jpg")) return "{$barcode}.jpg";
        if (Storage::disk('public')->exists("{$folder}/{$barcode}.png")) return "{$barcode}.png";
        return null;
    }

    // ==============================================================
    // FIM: FUNÇÕES DE SINCRONIZAÇÃO DE PRODUTOS E IMAGENS
    // ==============================================================

    public function mensagens(Request $request)
    {
        $codigo = $request->input('codigo');
        $mac = $request->input('mac');

        if (!$codigo || !$mac) {
            return response()->json(['error' => 'Código e MAC são obrigatórios'], 400);
        }

        $checkout = Checkout::where('codigo', $codigo)
            ->where('mac', $mac)
            ->where('status', 'ativo')
            ->first();

        if (!$checkout) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $loja = Loja::where('id', $checkout->licenca->loja_id)->first();

        if (!$loja) {
            return response()->json(['error' => 'Loja não encontrada'], 404);
        }

        $user_codigo = User::find($loja->user_id)->codigo;

        $mensagens = Mensagens::where(function ($query) use ($loja) {
            $query->whereNull('loja_codigo')
                ->orWhere('loja_codigo', $loja->codigo);
        })->where('ativo', true)
            ->get(['id', 'texto', 'tipo', 'ativo', 'loja_codigo']);

        return response()->json([
            'success' => 'Mensagens recuperadas com sucesso',
            'mensagens' => $mensagens,
            'user_codigo' => $user_codigo,
        ], 200);
    }

    public function clientes(Request $request)
    {
        $codigo = $request->input('codigo');
        $mac = $request->input('mac');
        if (!$codigo || !$mac) {
            return response()->json(['error' => 'Código e MAC são obrigatórios'], 400);
        }

        $checkout = Checkout::where('codigo', $codigo)
            ->where('mac', $mac)
            ->where('status', 'ativo')
            ->first();
        if (!$checkout) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $loja = Loja::where('id', $checkout->licenca->loja_id)->first();

        if (!$loja) {
            return response()->json(['error' => 'Loja não encontrada'], 404);
        }

        $clientes = Cliente::where('loja_id', $loja->id)->get();
        $clientes->makeHidden(['cpf', 'endereco', 'bairro', 'cidade', 'estado', 'cep']);

        return response()->json([
            'success' => 'Clientes recuperados com sucesso',
            'clientes' => $clientes,
        ]);
    }

    public function sync(Request $request)
    {
        $codigo = $request->input('codigo');
        $mac = $request->input('mac');

        if (!$codigo || !$mac) {
            return response()->json(['error' => 'Código e MAC são obrigatórios'], 400);
        }

        $checkout = Checkout::where('codigo', $codigo)
            ->where('mac', $mac)
            ->where('status', 'ativo')
            ->first();

        if (!$checkout) {
            return response()->json(['error' => 'Acesso negado ou PDV inativo'], 403);
        }

        $loja = Loja::where('id', $checkout->licenca->loja_id)->first();

        if (!$loja) {
            return response()->json(['error' => 'Loja não encontrada'], 404);
        }

        $transacoes = $request->input('transacoes', []);
        $uuids_confirmados = [];
        $clientes_afetados = [];

        DB::beginTransaction(); 

        try {
            foreach ($transacoes as $tr) {
                $existe = ClienteTransacao::where('uuid', $tr['uuid'])->exists();

                if ($existe) {
                    $uuids_confirmados[] = $tr['uuid'];
                    continue;
                }

                ClienteTransacao::create([
                    'loja_id' => $loja->id,
                    'uuid' => $tr['uuid'],
                    'cliente_codigo' => $tr['cliente_codigo'],
                    'venda_codigo' => $tr['venda_codigo'] ?? null,
                    'tipo' => $tr['tipo'], 
                    'valor' => $tr['valor'],
                    'data_hora' => $tr['data_hora'],
                    'usuario_codigo' => $tr['usuario_codigo'] ?? 'SYSTEM',
                    'checkout_mac' => $mac 
                ]);

                $cliente = Cliente::where('codigo', $tr['cliente_codigo'])
                    ->where('loja_id', $loja->id)
                    ->first();

                if ($cliente) {
                    if ($tr['tipo'] == 'debito') {
                        $cliente->credito_usado += $tr['valor'];
                        $cliente->saldo -= $tr['valor']; 
                    } elseif ($tr['tipo'] == 'pagamento' || $tr['tipo'] == 'estorno') {
                        $cliente->credito_usado -= $tr['valor'];
                        $cliente->saldo += $tr['valor'];
                    }

                    $cliente->save();
                    $clientes_afetados[] = $cliente->codigo; 
                }

                $uuids_confirmados[] = $tr['uuid'];
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sincronização processada com sucesso',
                'synced_uuids' => $uuids_confirmados, 
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro Sync Crédito Loja {$loja->id}: " . $e->getMessage());

            return response()->json([
                'error' => 'Erro interno ao processar transações',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function syncDashboard(Request $request)
    {
        $codigo = $request->input('codigo');
        $mac = $request->input('mac');

        if (!$codigo || !$mac) {
            return response()->json(['error' => 'Credenciais obrigatórias'], 400);
        }

        $checkout = Checkout::where('codigo', $codigo)
            ->where('mac', $mac)
            ->where('status', 'ativo')
            ->with('licenca')
            ->first();

        if (!$checkout || !$checkout->licenca) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $loja_id = $checkout->licenca->loja_id;
        $payload = $request->all();

        $synced_uuids = [
            'vendas' => [],
            'movimentacoes' => [],
            'caixa_sessoes' => [],
            'cancelamentos' => []
        ];

        DB::beginTransaction(); 

        try {
            if (!empty($payload['caixa_sessoes'])) {
                foreach ($payload['caixa_sessoes'] as $cx) {
                    LojaCaixaSessao::updateOrCreate(
                        ['uuid' => $cx['uuid']],
                        [
                            'loja_id' => $loja_id,
                            'operador' => $cx['operador'],
                            'usuario_codigo' => $cx['usuario_codigo'],
                            'abertura' => $cx['abertura'],
                            'fechamento' => $cx['fechamento'] ?? null,
                            'valor_abertura' => $cx['valor_abertura'],
                            'valor_fechamento' => $cx['valor_fechamento'] ?? null,
                            'status' => $cx['status'],
                            'checkout_mac' => $mac
                        ]
                    );
                    $synced_uuids['caixa_sessoes'][] = $cx['uuid'];
                }
            }

            if (!empty($payload['movimentacoes'])) {
                foreach ($payload['movimentacoes'] as $mv) {
                    LojaMovimentacao::updateOrCreate(
                        ['uuid' => $mv['uuid']],
                        [
                            'loja_id' => $loja_id,
                            'tipo' => $mv['tipo'],
                            'operador' => $mv['operador'],
                            'valor' => $mv['valor'],
                            'descricao' => $mv['descricao'] ?? '',
                            'data_hora' => $mv['data_hora'],
                            'checkout_mac' => $mac
                        ]
                    );
                    $synced_uuids['movimentacoes'][] = $mv['uuid'];
                }
            }

            if (!empty($payload['vendas'])) {
                foreach ($payload['vendas'] as $v) {
                    $vendaModel = LojaVenda::updateOrCreate(
                        ['uuid' => $v['uuid']],
                        [
                            'loja_id' => $loja_id,
                            'venda_codigo' => $v['venda_codigo'],
                            'operador' => $v['operador'],
                            'usuario_codigo' => $v['usuario_codigo'],
                            'data_hora' => $v['data_hora'],
                            'total' => $v['total'],
                            'metodo_pagamento' => $v['metodo_pagamento'],
                            'valor_recebido' => $v['valor_recebido'] ?? null,
                            'troco' => $v['troco'] ?? 0,
                            'tempo_atendimento' => $v['tempo_atendimento'] ?? 0,
                            'is_cliente' => $v['is_cliente'] ?? false,
                            'cliente_codigo' => $v['cliente_codigo'] ?? null,
                            'checkout_mac' => $mac
                        ]
                    );

                    if (!empty($v['itens'])) {
                        $vendaModel->itens()->delete(); 

                        $itensParaInserir = [];
                        foreach ($v['itens'] as $item) {
                            $itensParaInserir[] = [
                                'loja_venda_id' => $vendaModel->id,
                                'produto_nome' => $item['produto_nome'],
                                'codigo_barra' => $item['codigo_barra'] ?? null,
                                'categoria' => $item['categoria'] ?? 'Geral',
                                'quantidade' => $item['quantidade'],
                                'preco_unitario' => $item['preco_unitario'],
                                'subtotal' => $item['subtotal'],
                                'custo' => $item['custo'] ?? 0,
                            ];
                        }
                        LojaVendaItem::insert($itensParaInserir);
                    }
                    $synced_uuids['vendas'][] = $v['uuid'];
                }

                if (!empty($payload['cancelamentos'])) {
                    foreach ($payload['cancelamentos'] as $cancel) {
                        LojaCancelamento::updateOrCreate(
                            ['uuid' => $cancel['uuid']], 
                            [
                                'loja_id' => $loja_id,
                                'usuario_codigo' => $cancel['usuario_codigo'] ?? 'SISTEMA',
                                'venda_codigo' => $cancel['venda_codigo'] ?? null,
                                'produtos' => $cancel['produtos'] ?? [], 
                                'valor_total' => $cancel['valor_total'],
                                'data_hora' => $cancel['data_hora'],
                                'observacao' => $cancel['observacao'] ?? null,
                                'autorizado_por' => $cancel['autorizado_por'] ?? null,
                                'checkout_mac' => $mac
                            ]
                        );
                        $synced_uuids['cancelamentos'][] = $cancel['uuid'];
                    }
                }
            }

            DB::commit(); 

            return response()->json([
                'success' => true,
                'synced_uuids' => $synced_uuids
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro Sync Dashboard: " . $e->getMessage());
            return response()->json(['error' => 'Erro interno ao sincronizar', 'details' => $e->getMessage()], 500);
        }
    }

    public function autenticarPdv(Request $request)
    {
        $key = $request->header('Authorization');
        $key = str_replace('Bearer ', '', $key);

        if (!$key) {
            return response()->json(['error' => 'Chave de licença não fornecida'], 400);
        }

        $licenca = Licenca::where('key', $key)->first();
        if (!$licenca) {
            return response()->json(['error' => 'Licença Inválida!'], 403);
        }

        $loja = Loja::find($licenca->loja_id);
        if (!$loja) {
            return response()->json(['error' => 'Loja não encontrada.'], 404);
        }

        $dados = $request->all();
        
        $checkout = Checkout::where('licenca_id', $licenca->id)
            ->where('mac', $dados['mac'] ?? null)
            ->first();

        $novoStatus = ($licenca->isValid() && $checkout && $checkout->status === 'ativo') ? 'ativo' : 'inativo';
        
        if (!$checkout && $licenca->isValid()) {
            $ativos = Checkout::where('licenca_id', $licenca->id)->where('status', 'ativo')->count();
            if ($ativos < $licenca->limite_dispositivos) {
                $novoStatus = 'ativo';
            }
        }

        if (!$checkout) {
            $checkout = Checkout::create([
                'licenca_id' => $licenca->id,
                'codigo' => $dados['codigo'] ?? null,
                'descricao' => 'PDV ' . ($dados['hostname'] ?? 'Novo'),
                'mac' => $dados['mac'] ?? null,
                'status' => $novoStatus,
            ]);
        } else {
            $checkout->update(['status' => $novoStatus]);
        }

        return response()->json([
            'success' => 'PDV Autenticado',
            'loja_codigo' => $loja->codigo, 
            'status' => $checkout->status
        ]);
    }
}