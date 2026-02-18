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
use App\Models\ClienteTransacao; // Model da tabela espelho na nuvem
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
    public function index()
    {
        $checkouts = Checkout::with('licenca')->get();
        return view('checkouts.index', compact('checkouts'));
    }

    public function create()
    {
        $licencas = Licenca::where('status', 'ativa')->get();
        return view('checkouts.create', compact('licencas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'licenca_id' => 'required|exists:licencas,id',
            'descricao' => 'required',
        ]);

        $codigo = Str::random(30);

        $licenca = Licenca::find($request->licenca_id);

        if (!$licenca || $licenca->status !== 'ativa') {
            return back()->withErrors(['licenca_id' => 'Licença inválida ou inativa.']);
        }

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
        $checkout = Checkout::findOrFail($id);
        $licencas = Licenca::where('status', 'ativa')->get();
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

  
    public function licenca(Request $request)
    {
        $key = $request->header('Authorization');
        $key = str_replace('Bearer ', '', $key);

        if (!$key) {
            return response()->json(['error' => 'Chave de licença não fornecida'], 400);
        }

        $licenca = Licenca::where('key', $key)->first();

        if (!$licenca || $licenca->status !== 'ativo') {
            return response()->json(['error' => 'Licença inválida ou inativa'], 403);
        }

        $dadosMaquina = $request->all();
        $checkout = Checkout::where('licenca_id', $licenca->id)->first();

        if ($checkout) {
            return response()->json([
                'success' => 'Conexão bem-sucedida',
                'mensagem' => 'Já existe um checkout registrado para esta licença.',
                'validade' => $licenca->validade,  // Adicionado
                'status' => $licenca->status,      // Adicionado
            ]);
        }

        $checkout = Checkout::create([
            'licenca_id' => $licenca->id,
            'codigo' => $dadosMaquina['codigo'] ?? null,
            'sistema_operacional' => $dadosMaquina['sistema_operacional'] ?? null,
            'versao_sistema' => $dadosMaquina['versao_sistema'] ?? null,
            'arquitetura' => $dadosMaquina['arquitetura'] ?? null,
            'hostname' => $dadosMaquina['hostname'] ?? null,
            'ip' => $dadosMaquina['ip'] ?? null,
            'mac' => $dadosMaquina['mac'] ?? null,
            'status' => 'ativo',
        ]);

        return response()->json([
            'success' => 'Dados salvos com sucesso',
            'checkout' => $checkout,
            'validade' => $licenca->validade,  // Adicionado
            'status' => $licenca->status,      // Adicionado
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

    // Busca o código do usuário associado à loja
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
            Log::error('Código ou MAC ausentes na requisição', ['codigo' => $codigo, 'mac' => $mac]);
            return response()->json(['error' => 'Código e MAC são obrigatórios'], 400);
        }

        
        $checkout = Checkout::where('codigo', $codigo)
                            ->where('mac', $mac)
                            ->where('status', 'ativo')
                            ->first();
        if (!$checkout) {
            Log::error('Checkout não encontrado ou inativo', ['codigo' => $codigo, 'mac' => $mac]);
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $loja = Loja::where('id', $checkout->licenca->loja_id)->first();

        if (!$loja) {
            Log::error('Loja não encontrada', ['licenca_id' => $checkout->licenca->loja_id]);
            return response()->json(['error' => 'Loja não encontrada'], 404);
        }
     /// buscando produtos pelo id da loja 

        $user_codigo = User::find($loja->user_id)->codigo;
//dd($user_codigo);

        // Busca os produtos e seus lotes
        $produtos = Produto::where('loja_id', $loja->id)
                           ->with('lotes')
                           ->get();
        //dd ($produtos);

        // Cria a pasta do usuário no storage
        $userStoragePath = "public/lojas/{$loja->codigo}";
        Storage::makeDirectory($userStoragePath);

        // Remove ZIP e token antigos, se existirem
        $oldZipPath = storage_path("app/{$userStoragePath}/{$loja->codigo}.zip");
        if (file_exists($oldZipPath)) {
            unlink($oldZipPath);
            Log::info('ZIP antigo removido', ['path' => $oldZipPath]);
        }

        $oldTokenPath = storage_path("app/{$userStoragePath}/{$loja->codigo}_token.txt");
        if (file_exists($oldTokenPath)) {
            unlink($oldTokenPath);
            Log::info('Token antigo removido', ['path' => $oldTokenPath]);
        }

        // Gera o arquivo ZIP compactando a pasta produtos inteira
        $zipFileName = "{$loja->codigo}.zip";
        $zipPath = storage_path("app/{$userStoragePath}/{$zipFileName}");
        // Ajusta o caminho para a pasta de imagens no storage
        $imagesDir = storage_path("app/public/lojas/{$loja->codigo}/produtos");

        // Verifica se a pasta de imagens existe
        if (!is_dir($imagesDir)) {
            Log::warning('Pasta de imagens não encontrada', ['path' => $imagesDir]);
            // Cria um ZIP vazio com um diretório vazio para evitar erros
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                Log::error('Falha ao criar arquivo ZIP', ['path' => $zipPath]);
                return response()->json(['error' => 'Falha ao criar arquivo ZIP'], 500);
            }
            $zip->addEmptyDir('empty');
            $zip->close();
          #  Log::info('ZIP criado com diretório vazio', ['path' => $zipPath]);
        } else {
            // Compacta a pasta inteira
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
           #     Log::error('Falha ao criar arquivo ZIP', ['path' => $zipPath]);
                return response()->json(['error' => 'Falha ao criar arquivo ZIP'], 500);
            }

            // Função para adicionar arquivos e subdiretórios ao ZIP
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($imagesDir),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            $imagesAdded = 0;
            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    // Calcula o caminho relativo para manter a estrutura no ZIP
                    $relativePath = substr($filePath, strlen($imagesDir) + 1);
                    $zip->addFile($filePath, $relativePath);
                    $imagesAdded++;
                #    Log::info('Arquivo adicionado ao ZIP', ['file' => $relativePath, 'path' => $filePath]);
                }
            }

            // Fecha o ZIP e verifica se foi bem-sucedido
            if (!$zip->close()) {
            #    Log::error('Falha ao fechar o arquivo ZIP', ['path' => $zipPath]);
                return response()->json(['error' => 'Falha ao fechar o arquivo ZIP'], 500);
            }

            Log::info('ZIP criado com sucesso', [
                'path' => $zipPath,
                'images_added' => $imagesAdded
            ]);
        }

        // Verifica se o ZIP foi realmente criado
        if (!file_exists($zipPath)) {
        #    Log::error('Arquivo ZIP não foi criado', ['path' => $zipPath]);
            return response()->json(['error' => 'Falha ao criar arquivo ZIP: arquivo não encontrado'], 500);
        }

        // Gera um token temporário para autenticação do download
        $token = Str::random(32);
        $tokenPath = storage_path("app/{$userStoragePath}/{$loja->codigo}_token.txt");
        file_put_contents($tokenPath, $token);
        Log::info('Token gerado', ['path' => $tokenPath, 'token' => $token]);

        // Gera a URL para download do ZIP com o token
        $zipUrl = url("/api/download-zip/{$loja->codigo}?token={$token}");

       Log::info('URL do ZIP gerada', ['url' => $zipUrl]);

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

        //dd ( $token );

        // Verifica se o token é válido
        $tokenPath = storage_path("app/public/lojas/{$user_codigo}/{$user_codigo}_token.txt");
        if (!file_exists($tokenPath)) {
        #    Log::error('Token não encontrado', ['user_codigo' => $user_codigo, 'token' => $token]);
            return response()->json(['error' => 'Token inválido ou expirado'], 403);
        }

        $storedToken = file_get_contents($tokenPath);
        if ($token !== $storedToken) {
        #    Log::error('Token inválido', ['user_codigo' => $user_codigo, 'token' => $token, 'stored_token' => $storedToken]);
            return response()->json(['error' => 'Token inválido'], 403);
        }

        // Verifica se o user_codigo da requisição coincide com o da loja
        $codigo = $request->header('X-Codigo');
        $mac = $request->header('X-Mac');

        if (!$codigo || !$mac) {
        #    Log::error('Código ou MAC ausentes nos cabeçalhos', ['codigo' => $codigo, 'mac' => $mac]);
            return response()->json(['error' => 'Código e MAC são obrigatórios'], 400);
        }

        $checkout = Checkout::where('codigo', $codigo)
                            ->where('mac', $mac)
                            ->where('status', 'ativo')
                            ->first();

        if (!$checkout) {
            Log::error('Checkout não encontrado ou inativo', ['codigo' => $codigo, 'mac' => $mac]);
            return response()->json(['error' => 'Acesso negado'], 403);
        }

       
        // Fornece o arquivo ZIP
        $zipPath = storage_path("app/public/lojas/{$user_codigo}/{$user_codigo}.zip");
        if (!file_exists($zipPath)) {
         #   Log::error('Arquivo ZIP não encontrado no download', ['path' => $zipPath]);
            return response()->json(['error' => 'Arquivo ZIP não encontrado'], 404);
        }

        // Remove o token após o download
        unlink($tokenPath);
    #    Log::info('Token removido após download', ['path' => $tokenPath]);

        Log::info('ZIP baixado com sucesso', ['user_codigo' => $user_codigo, 'path' => $zipPath]);

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

        // Busca os funcionários associados à loja via LojaPermissao
        $funcionarios = User::whereHas('permissoes', function ($query) use ($loja) {
            $query->where('loja_id', $loja->id);
        })->get(['id', 'name', 'usuario', 'email', 'imagem', 'password', 'codigo', 'endereco', 'bairro', 'cidade', 'estado', 'cep', 'avatar', 'phone', 'about_me', 'funcao', 'acesso']); // Adicionado 'acesso'

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

        // Busca as chaves de cancelamento da loja
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

    public function uploadImagensZip(Request $request)
    {
        // Valida os parâmetros obrigatórios
        $codigo = $request->input('codigo');
        $mac = $request->input('mac');
        if (!$codigo || !$mac) {
            return response()->json(['error' => 'Código e MAC são obrigatórios'], 400);
        }

        // Verifica o checkout
        $checkout = Checkout::where('codigo', $codigo)
                            ->where('mac', $mac)
                            ->where('status', 'ativo')
                            ->first();
        if (!$checkout) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        // Verifica a loja
        $loja = Loja::where('id', $checkout->licenca->loja_id)->first();
        if (!$loja) {
            return response()->json(['error' => 'Loja não encontrada'], 404);
        }

        // Verifica o usuário
        $user_codigo = $request->input('user_codigo');
        $user = User::where('codigo', $user_codigo)->first();
        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        // Processa o arquivo ZIP
        $tempDir = storage_path('app/temp/' . uniqid());
        $extractedImages = [];
        if ($request->hasFile('imagens_zip')) {
            $zipFile = $request->file('imagens_zip');
            $zipPath = $zipFile->storeAs('temp', $zipFile->getClientOriginalName());

            // Descompacta o arquivo ZIP
            $zip = new ZipArchive;
            if ($zip->open(storage_path('app/' . $zipPath)) === TRUE) {
                // Cria diretório temporário para extrair as imagens
                if (!file_exists($tempDir)) {
                    mkdir($tempDir, 0777, true);
                }

                $zip->extractTo($tempDir);
                $zip->close();

                // Lista as imagens extraídas (apenas .jpg e .png)
                $extractedImages = glob($tempDir . '/*.{jpg,png}', GLOB_BRACE);
                Log::info('Imagens extraídas do ZIP: ' . implode(', ', $extractedImages));
            } else {
        #        Log::error('Falha ao abrir o arquivo ZIP: ' . $zipFile->getClientOriginalName());
                return response()->json(['error' => 'Falha ao processar o arquivo ZIP'], 400);
            }
        } else {
        #    Log::warning('Nenhum arquivo ZIP recebido no campo imagens_zip');
            return response()->json(['error' => 'Nenhum arquivo ZIP enviado'], 400);
        }

        // Processa as imagens extraídas
        $processedImages = [];
        foreach ($extractedImages as $imagePath) {
            $filename = basename($imagePath);
            $barcode = pathinfo($filename, PATHINFO_FILENAME); // Extrai o código de barras do nome do arquivo
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);

            // Salva a imagem em produtos_full
            $fullFilename = "{$barcode}.{$extension}";
            Storage::disk('public')->put("produtos_full/{$fullFilename}", file_get_contents($imagePath));
        #    Log::info("Imagem salva em produtos_full: {$fullFilename}");

            // Salva a imagem no diretório do usuário
            Storage::disk('public')->makeDirectory("lojas/{$loja->codigo}/produtos");
            Storage::disk('public')->put("lojas/{$loja->codigo}/produtos/{$fullFilename}", file_get_contents($imagePath));
        #    Log::info("Imagem salva em usuario/{$loja->codigo}/produtos: {$fullFilename}");

            $processedImages[$barcode] = $fullFilename;
        }

        // Limpa o diretório temporário
        if (file_exists($tempDir)) {
            array_map('unlink', glob("$tempDir/*.*"));
            rmdir($tempDir);
        }
        if (isset($zipPath) && Storage::exists($zipPath)) {
            Storage::delete($zipPath);
        }

        return response()->json([
            'success' => 'Imagens processadas com sucesso',
            'images' => $processedImages
        ], 201);
    }

    public function storeProdutosUnified(Request $request)
    {
        // Valida os parâmetros obrigatórios
        $codigo = $request->input('codigo');
        $mac = $request->input('mac');
        if (!$codigo || !$mac) {
            return response()->json(['error' => 'Código e MAC são obrigatórios'], 400);
        }

        // Verifica o checkout
        $checkout = Checkout::where('codigo', $codigo)
                            ->where('mac', $mac)
                            ->where('status', 'ativo')
                            ->first();
        if (!$checkout) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        // Verifica a loja
        $loja = Loja::where('id', $checkout->licenca->loja_id)->first();
        if (!$loja) {
            return response()->json(['error' => 'Loja não encontrada'], 404);
        }

        // Verifica o usuário
        $user_codigo = $request->input('user_codigo');
        $user = User::where('codigo', $user_codigo)->first();
        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        // Valida o JSON de produtos
        $productsJson = $request->input('products');
     #   Log::info('Received products: ' . $productsJson);
        if (empty($productsJson)) {
            return response()->json(['error' => 'O campo "products" é obrigatório'], 400);
        }

        $products = json_decode($productsJson, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($products)) {
    #        Log::error('JSON decode error: ' . json_last_error_msg());
            return response()->json(['error' => 'O campo "products" deve ser um JSON válido'], 400);
        }

        $response = [
            'produtos_full' => [],
            'produtos' => []
        ];

        // Processa cada produto
        foreach ($products as $index => $product) {
            if (!isset($product['codigo_barra'])) {
                return response()->json(['error' => "O campo 'codigo_barra' é obrigatório para o produto no índice {$index}"], 400);
            }

            $barcode = $product['codigo_barra'];
            $produto_full_id = null;

            // Busca ou cria o ProdutoFull
            $produto_full = ProdutoFull::where('codigo_barra', $barcode)->first();
            if (!$produto_full) {
                $full_data = [
                    'codigo_barra' => $barcode,
                    'nome' => $product['nome'] ?? '',
                    'categoria' => $product['categoria'] ?? '',
                    'descricao' => $product['descricao'] ?? '',
                    'peso' => $product['peso'] ?? 0,
                ];

                // Verifica se a imagem já foi salva pelo endpoint /upload-imagens-zip
                $imagePathFull = Storage::disk('public')->exists("produtos_full/{$barcode}.jpg") ? "{$barcode}.jpg" :
                                 (Storage::disk('public')->exists("produtos_full/{$barcode}.png") ? "{$barcode}.png" : null);
                if ($imagePathFull) {
                    $full_data['imagem'] = $imagePathFull;
            #        Log::info("Imagem associada em produtos_full: {$imagePathFull}");
                }

                $produto_full = ProdutoFull::create($full_data);
                $produto_full_id = $produto_full->id;
                $response['produtos_full'][$barcode] = $produto_full_id;
            } else {
                $produto_full_id = $produto_full->id;
                $response['produtos_full'][$barcode] = $produto_full_id;
            }

            // Busca ou cria o Produto do usuário
            $produto = Produto::where('user_id', $user->id)
                              ->where('produto_full_id', $produto_full_id)
                              ->first();
            if (!$produto) {
                $produto_data = $product;
                $produto_data['user_id'] = $user->id;
                $produto_data['produto_full_id'] = $produto_full_id;

                // Verifica se a imagem já foi salva pelo endpoint /upload-imagens-zip
                $imagePathUser = Storage::disk('public')->exists("lojas/{$loja->codigo}/produtos/{$barcode}.jpg") ? "{$barcode}.jpg" :
                                 (Storage::disk('public')->exists("lojas/{$loja->codigo}/produtos/{$barcode}.png") ? "{$barcode}.png" : null);
                if ($imagePathUser) {
                    $produto_data['imagem'] = $imagePathUser;
                #    Log::info("Imagem associada em lojas/{$loja->codigo}/produtos: {$imagePathUser}");
                }

                $produto = Produto::create($produto_data);
                $response['produtos'][$barcode] = $produto->id;
            } else {
                $response['produtos'][$barcode] = $produto->id;
            }
        }

        return response()->json([
            'success' => 'Processamento concluído',
            'data' => $response
        ], 201);
    }

      public function mensagens(Request $request)
{
    $codigo = $request->input('codigo');
    $mac = $request->input('mac');

    if (!$codigo || !$mac) {
        Log::error('Código ou MAC ausentes na requisição', ['codigo' => $codigo, 'mac' => $mac]);
        return response()->json(['error' => 'Código e MAC são obrigatórios'], 400);
    }

    $checkout = Checkout::where('codigo', $codigo)
                        ->where('mac', $mac)
                        ->where('status', 'ativo')
                        ->first();

    if (!$checkout) {
        Log::error('Checkout não encontrado ou inativo', ['codigo' => $codigo, 'mac' => $mac]);
        return response()->json(['error' => 'Acesso negado'], 403);
    }

    $loja = Loja::where('id', $checkout->licenca->loja_id)->first();

    if (!$loja) {
        Log::error('Loja não encontrada', ['licenca_id' => $checkout->licenca->loja_id]);
        return response()->json(['error' => 'Loja não encontrada'], 404);
    }

    $user_codigo = User::find($loja->user_id)->codigo;

    // Busca mensagens globais (loja_codigo nulo) e personalizadas para a loja
    $mensagens = Mensagens::where(function ($query) use ($loja) {
        $query->whereNull('loja_codigo')
              ->orWhere('loja_codigo', $loja->codigo);
    })->where('ativo', true)
      ->get(['id', 'texto', 'tipo', 'ativo', 'loja_codigo']);

    Log::info('Mensagens recuperadas com sucesso', [
        'loja_id' => $loja->id,
        'total_mensagens' => $mensagens->count(),
    ]);

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
        // Validação do Checkout/PDV
        $checkout = Checkout::where('codigo', $codigo)
                            ->where('mac', $mac)
                            ->where('status', 'ativo')
                            ->first();
        if (!$checkout) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }
        // Identifica a loja associada à licença do checkout
        $loja = Loja::where('id', $checkout->licenca->loja_id)->first();
        
        if (!$loja) {
             return response()->json(['error' => 'Loja não encontrada'], 404);
        }
        // Busca os clientes da loja
        // O Laravel descriptografa automaticamente os campos 'encrypted' ao serializar para JSON?
        // NÃO, por padrão o Laravel esconde campos 'hidden' ou retorna o valor do atributo.
        // Como usamos o cast 'encrypted', ao acessar $cliente->cpf, ele retorna o valor descriptografado.
        // Ao converter para array/JSON, ele usa esses valores descriptografados.
        // PORTANTO, o JSON de resposta enviará os dados "abertos" via HTTPS para o PDV,
        // que é o comportamento desejado para que o PDV possa ler os dados.
        // A criptografia acontece apenas no banco de dados do servidor (data at rest).
        

        
        $clientes = Cliente::where('loja_id', $loja->id)->get();
        
        // Oculta os campos sensíveis apenas para esta resposta
        $clientes->makeHidden(['cpf', 'endereco', 'bairro', 'cidade', 'estado', 'cep']);


        return response()->json([
            'success' => 'Clientes recuperados com sucesso',
            'clientes' => $clientes,
        ]);
    }


public function sync(Request $request)
    {
        // ---------------------------------------------------------
        // 1. SEGURANÇA (Idêntica ao método Clientes)
        // ---------------------------------------------------------
        $codigo = $request->input('codigo');
        $mac = $request->input('mac');

        if (!$codigo || !$mac) {
            return response()->json(['error' => 'Código e MAC são obrigatórios'], 400);
        }

        // Validação do Checkout/PDV
        $checkout = Checkout::where('codigo', $codigo)
                            ->where('mac', $mac)
                            ->where('status', 'ativo')
                            ->first();

        if (!$checkout) {
            return response()->json(['error' => 'Acesso negado ou PDV inativo'], 403);
        }

        // Identifica a loja associada
        $loja = Loja::where('id', $checkout->licenca->loja_id)->first();

        if (!$loja) {
             return response()->json(['error' => 'Loja não encontrada'], 404);
        }

        // ---------------------------------------------------------
        // 2. PROCESSAMENTO DAS TRANSAÇÕES
        // ---------------------------------------------------------
        
        // Recebe o array de transações enviado pelo Python
        $transacoes = $request->input('transacoes', []);
        $uuids_confirmados = [];
        $clientes_afetados = [];

        DB::beginTransaction(); // Garante que ou salva tudo ou não salva nada

        try {
            foreach ($transacoes as $tr) {
                
                // A. Verifica se essa transação já foi processada antes (Proteção contra duplicidade)
                // Usamos o UUID gerado pelo Python como chave única
                $existe = ClienteTransacao::where('uuid', $tr['uuid'])->exists();

                if ($existe) {
                    // Se já existe, apenas confirmamos para o PDV não enviar de novo
                    $uuids_confirmados[] = $tr['uuid'];
                    continue; 
                }

                // B. Salva a transação no histórico da Nuvem
                ClienteTransacao::create([
                    'loja_id'        => $loja->id,
                    'uuid'           => $tr['uuid'],
                    'cliente_codigo' => $tr['cliente_codigo'],
                    'venda_codigo'   => $tr['venda_codigo'] ?? null,
                    'tipo'           => $tr['tipo'], // 'debito' (compra) ou 'pagamento' (quitação)
                    'valor'          => $tr['valor'],
                    'data_hora'      => $tr['data_hora'],
                    'usuario_codigo' => $tr['usuario_codigo'] ?? 'SYSTEM',
                    'checkout_mac'   => $mac // Rastreabilidade: qual caixa enviou
                ]);

                // C. Atualiza o Saldo Mestre do Cliente (A fonte da verdade)
                $cliente = Cliente::where('codigo', $tr['cliente_codigo'])
                                  ->where('loja_id', $loja->id)
                                  ->first();

                if ($cliente) {
                    // Lógica Financeira
                    if ($tr['tipo'] == 'debito') {
                        // Cliente comprou fiado: Aumenta dívida, diminui saldo disponível
                        $cliente->credito_usado += $tr['valor'];
                        $cliente->saldo -= $tr['valor']; // Pode ficar negativo se estourar limite offline
                    } 
                    elseif ($tr['tipo'] == 'pagamento' || $tr['tipo'] == 'estorno') {
                        // Cliente pagou: Diminui dívida, libera saldo
                        $cliente->credito_usado -= $tr['valor'];
                        $cliente->saldo += $tr['valor'];
                    }
                    
                    $cliente->save();
                    $clientes_afetados[] = $cliente->codigo; // Guarda para log ou retorno futuro
                }

                // Adiciona na lista de sucesso
                $uuids_confirmados[] = $tr['uuid'];
            }

            DB::commit();

            // ---------------------------------------------------------
            // 3. RETORNO PARA O PDV
            // ---------------------------------------------------------
            return response()->json([
                'success' => true,
                'message' => 'Sincronização processada com sucesso',
                'synced_uuids' => $uuids_confirmados, // O PDV usará isso para marcar como 'synced'
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
        // 1. Validação de Segurança (Código e MAC)
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

        // Arrays para confirmar o sucesso para o PDV
        $synced_uuids = [
            'vendas' => [],
            'movimentacoes' => [],
            'caixa_sessoes' => [],
            'cancelamentos' => []

        ];

        DB::beginTransaction(); // Início da transação segura

        try {
            // A. Sincronizar Sessões de Caixa
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

            // B. Sincronizar Movimentações (Sangria/Suprimento)
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

            // C. Sincronizar Vendas e Itens
            if (!empty($payload['vendas'])) {
                foreach ($payload['vendas'] as $v) {
                    // 1. Salva Cabeçalho
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

                    // 2. Processa Itens (Deleta antigos e insere novos para garantir integridade)
                    if (!empty($v['itens'])) {
                        $vendaModel->itens()->delete(); // Limpa itens anteriores dessa venda

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
                        // Insert rápido em lote
                        LojaVendaItem::insert($itensParaInserir);
                    }
                    $synced_uuids['vendas'][] = $v['uuid'];
                }

                    // D. SINCRONIZAR CANCELAMENTOS
                    if (!empty($payload['cancelamentos'])) {
                        foreach ($payload['cancelamentos'] as $cancel) {
                            LojaCancelamento::updateOrCreate(
                                ['uuid' => $cancel['uuid']], // Busca pelo UUID único
                                [
                                    'loja_id'        => $loja_id,
                                    'usuario_codigo' => $cancel['usuario_codigo'] ?? 'SISTEMA',
                                    'venda_codigo'   => $cancel['venda_codigo'] ?? null,
                                    'produtos'       => $cancel['produtos'] ?? [], // O Model converte array para JSON autom.
                                    'valor_total'    => $cancel['valor_total'],
                                    'data_hora'      => $cancel['data_hora'],
                                    'observacao'     => $cancel['observacao'] ?? null,
                                    'autorizado_por' => $cancel['autorizado_por'] ?? null,
                                    'checkout_mac'   => $mac
                                ]
                            );
                            // Adiciona na lista de confirmação para o PDV
                            $synced_uuids['cancelamentos'][] = $cancel['uuid'];
                        }
                    }

            }

            DB::commit(); // Confirma gravação

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
}