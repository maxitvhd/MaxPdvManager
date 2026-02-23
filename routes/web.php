<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\LicencaController;
use App\Http\Controllers\LojasController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CancelamentoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MaxDivulgaAdminController;
use App\Http\Controllers\MaxDivulgaController;

#----- bibliotecas para resetar cache spatie -----
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\PermissionRegistrar;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', [HomeController::class, 'home']);
    Route::get('dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    # ======= rotas de demonstracao retira do menu principal ============
    Route::get('billing', function () {
        return view('billing');
    })->name('billing');

    Route::get('profile', function () {
        return view('profile');
    })->name('profile');

    Route::get('rtl', function () {
        return view('rtl');
    })->name('rtl');

    Route::middleware(['role:admin|super-admin'])->group(function () {
        Route::resource('usuarios', UsuarioController::class)->except(['create', 'store', 'show']);
    });

    Route::get('tables', function () {
        return view('tables');
    })->name('tables');

    Route::get('virtual-reality', function () {
        return view('virtual-reality');
    })->name('virtual-reality');

    Route::get('static-sign-in', function () {
        return view('static-sign-in');
    })->name('sign-in');

    Route::get('static-sign-up', function () {
        return view('static-sign-up');
    })->name('sign-up');
    #  ====== fim das rotas de demonstração ==========

    Route::get('/logout', [SessionsController::class, 'destroy']);
    Route::get('/user-profile', [InfoUserController::class, 'create']);
    Route::post('/user-profile', [InfoUserController::class, 'store']);
    Route::get('/login', function () {
        return view('dashboard');
    })->name('sign-up');
});

//  rota oficial de login Painel
Route::get('/login', function () {
    return view('session/login-session');
})->name('login.view');

#------------ ROTAS DE AUTENTICAÇÃO ------------------
Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/session', [SessionsController::class, 'store']);
    Route::get('/login/forgot-password', [ResetController::class, 'create']);
    Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
    Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
    Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');

    #------------ ROTAS DO APLICATIVO LEITOR ------------------
    Route::post('/app/login', [SessionsController::class, 'app'])->name('app.login');
});

Route::get('/app', [ProdutoController::class, 'AppLeitor'])
    ->middleware('app.auth')
    ->name('app.pdv');

#------------ ROTAS DE PRODUTOS ------------------
Route::group(['middleware' => 'auth'], function () {
    Route::post('/produtos/buscar', [ProdutoController::class, 'buscarProdutoPorCodigo'])->name('produtos.buscarPorCodigo');
    Route::resource('produtos', ProdutoController::class);
});

#------------ ROTAS DE LICENÇAS, LOJAS E CHECKOUTS ------------------
Route::group(['middleware' => 'auth'], function () {
    Route::get('licencas', [LicencaController::class, 'index'])->name('licencas.index');
    Route::get('licencas/criar', [LicencaController::class, 'create'])->name('licencas.create');
    Route::post('licencas', [LicencaController::class, 'store'])->name('licencas.store');
    Route::get('licencas/{codigo}/editar', [LicencaController::class, 'edit'])->name('licencas.edit');
    Route::put('licencas/{codigo}', [LicencaController::class, 'update'])->name('licencas.update');
    Route::delete('licencas/{codigo}', [LicencaController::class, 'destroy'])->name('licencas.destroy');
    Route::resource('lojas', LojasController::class);
    Route::patch('checkouts/{checkout}/toggle', [CheckoutController::class, 'toggleStatus'])->name('checkouts.toggleStatus');
    Route::resource('checkouts', CheckoutController::class);
});

#------------ ROTAS DE FUNCIONÁRIOS E PERMISSÕES --------------------------
Route::group(['middleware' => 'auth'], function () {

    // Rota Principal (Listagem e Filtro)
    Route::get('/funcionarios', [FuncionarioController::class, 'index'])->name('funcionarios.index');

    // Rotas de Ação (Adicionar, Editar, Excluir)
    // Usamos 'lojaCodigo' para deixar claro para o Laravel que é uma string
    Route::prefix('lojas/{lojaCodigo}')->group(function () {

        // Rota auxiliar para redirecionar quem acessa via URL direta
        Route::get('/funcionarios', [FuncionarioController::class, 'lojaFuncionarios'])->name('lojas.funcionarios.index');

        // Adicionar Funcionário
        Route::post('/funcionarios', [FuncionarioController::class, 'store'])->name('lojas.funcionarios.store');

        // Atualizar Permissão
        Route::put('/permissoes/{permissao}', [FuncionarioController::class, 'updatePermissao'])->name('lojas.permissoes.update');

        // Excluir Permissão
        Route::delete('/permissoes/{permissao}', [FuncionarioController::class, 'destroyPermissao'])->name('lojas.permissoes.destroy');
    });
});


#------------ ROTAS DE CANCELAMENTO DE CHAVES ------------------
Route::prefix('lojas/{loja}')->group(function () {
    Route::get('/cancelamento', [CancelamentoController::class, 'index'])->name('lojas.cancelamento.index');
    Route::get('/cancelamento/chaves', [CancelamentoController::class, 'chaves'])->name('lojas.cancelamento.chaves');
    Route::get('/cancelamento/chaves/create', [CancelamentoController::class, 'createChave'])->name('lojas.cancelamento.chaves.create');
    Route::post('/cancelamento/chaves', [CancelamentoController::class, 'storeChave'])->name('lojas.cancelamento.chaves.store');
    Route::get('/cancelamento/chaves/{chave}/edit', [CancelamentoController::class, 'editChave'])->name('lojas.cancelamento.chaves.edit');
    Route::put('/cancelamento/chaves/{chave}', [CancelamentoController::class, 'updateChave'])->name('lojas.cancelamento.chaves.update');
    Route::delete('/cancelamento/chaves/{chave}', [CancelamentoController::class, 'destroyChave'])->name('lojas.cancelamento.chaves.destroy');
});

#------------ ROTAS DE API PARA CHECKOUT ------------------
Route::post('/api/mensagens', [CheckoutController::class, 'mensagens']);
Route::post('/api/licenca', [CheckoutController::class, 'licenca']);
Route::post('/api/dados', [CheckoutController::class, 'dados']);
Route::post('/api/produtos', [CheckoutController::class, 'produtos']);
Route::post('/api/funcionarios', [CheckoutController::class, 'funcionarios']);
Route::post('/api/clientes', [CheckoutController::class, 'clientes']);
// Rota para sincronização de transações de crédito (POST)
Route::post('/api/credito/sincronizar', [CheckoutController::class, 'sync']);
Route::post('/api/cancelamento', [CheckoutController::class, 'cancelamentoChaves']);
Route::post('/api/dashboard/sincronizar', [CheckoutController::class, 'syncDashboard']);
Route::get('/api/download-zip/{user_codigo}', [CheckoutController::class, 'downloadZip']);

Route::post('/api/store-produtos-unified', [CheckoutController::class, 'storeProdutosUnified']);
Route::post('/api/upload-imagens-zip', [CheckoutController::class, 'uploadImagensZip']);

// Sistema de dados e  DashBoard 

Route::middleware(['auth'])->group(function () {
    // Dashboard Principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Relatórios Avançados
    Route::get('/dashboard/operacional', [DashboardController::class, 'operacional'])->name('dashboard.operacional');
    Route::get('/dashboard/financeiro', [DashboardController::class, 'financeiro'])->name('dashboard.financeiro');

    // Módulo Financeiro / Planos / Pagamentos
    Route::prefix('admin')->middleware(['role:admin|super-admin'])->group(function () {
        Route::resource('planos', \App\Http\Controllers\SistemaPlanoController::class);
        Route::resource('adicionais', \App\Http\Controllers\SistemaAdicionalController::class);

        // MaxDivulga Administrativo
        Route::get('/MaxDivulga', [MaxDivulgaAdminController::class, 'index'])->name('admin.maxdivulga.index');
        Route::post('/MaxDivulga/config', [MaxDivulgaAdminController::class, 'storeConfig'])->name('admin.maxdivulga.store_config');
        Route::get('/MaxDivulga/themes', [MaxDivulgaAdminController::class, 'themes'])->name('admin.maxdivulga.themes');
        Route::post('/MaxDivulga/themes', [MaxDivulgaAdminController::class, 'storeTheme'])->name('admin.maxdivulga.store_theme');
        // AI Theme Generator
        Route::get('/MaxDivulga/themes/create-ai', [MaxDivulgaAdminController::class, 'themeCreateAi'])->name('admin.maxdivulga.theme_create_ai');
        Route::post('/MaxDivulga/themes/generate-ai', [MaxDivulgaAdminController::class, 'themeGenerateAi'])->name('admin.maxdivulga.theme_generate_ai');
        Route::post('/MaxDivulga/themes/save-ai', [MaxDivulgaAdminController::class, 'themeSaveAi'])->name('admin.maxdivulga.theme_save_ai');
    });

    Route::get('/pagamentos/configuracoes', [\App\Http\Controllers\PagamentosController::class, 'configuracoesAdmin'])->name('pagamentos.configuracoes');
    Route::post('/pagamentos/configuracoes', [\App\Http\Controllers\PagamentosController::class, 'salvarConfiguracoes']);
    Route::get('/pagamentos/faturas', [\App\Http\Controllers\PagamentosController::class, 'indexFaturas'])->name('pagamentos.faturas');
    Route::get('/pagamentos/faturas/{pagamento}/gerar', [\App\Http\Controllers\PagamentosController::class, 'gerarFatura'])->name('pagamentos.gerar');
    Route::get('/pagamentos/faturas/{pagamento}/status', [\App\Http\Controllers\PagamentosController::class, 'consultarStatus'])->name('pagamentos.status_json');
    Route::post('/pagamentos/transacoes/{transacao}/estornar', [\App\Http\Controllers\PagamentosController::class, 'reembolsar'])->name('pagamentos.estornar');

    // Carrinho Vitrine (Planos + Adicionais)
    Route::get('/assinaturas/{licenca}', [\App\Http\Controllers\AssinaturaController::class, 'index'])->name('assinaturas.index');
    Route::post('/assinaturas/{licenca}/checkout', [\App\Http\Controllers\AssinaturaController::class, 'checkout'])->name('assinaturas.checkout');

    // Módulo Lojista MaxDivulga
    Route::prefix('lojista/maxdivulga')->group(function () {
        Route::get('/', [MaxDivulgaController::class, 'index'])->name('lojista.maxdivulga.index');
        Route::get('/create', [MaxDivulgaController::class, 'create'])->name('lojista.maxdivulga.create');
        Route::post('/store', [MaxDivulgaController::class, 'store'])->name('lojista.maxdivulga.store');
        Route::get('/{campaign}/show', [MaxDivulgaController::class, 'show'])->name('lojista.maxdivulga.show');
        Route::get('/{campaign}/edit', [MaxDivulgaController::class, 'edit'])->name('lojista.maxdivulga.edit');
        Route::put('/{campaign}', [MaxDivulgaController::class, 'update'])->name('lojista.maxdivulga.update');
        Route::put('/{campaign}/schedule', [MaxDivulgaController::class, 'updateSchedule'])->name('lojista.maxdivulga.updateSchedule');
        Route::post('/{campaign}/toggle-active', [MaxDivulgaController::class, 'toggleActive'])->name('lojista.maxdivulga.toggle_active');
        Route::delete('/{campaign}', [MaxDivulgaController::class, 'destroy'])->name('lojista.maxdivulga.destroy');
        Route::get('/{campaign}/download', [MaxDivulgaController::class, 'download'])->name('lojista.maxdivulga.download');
        Route::get('/api/produtos', [MaxDivulgaController::class, 'apiProducts'])->name('lojista.maxdivulga.api_products');

        // Theme Studio (todos os lojistas podem pré-visualizar)
        Route::get('/themes', [MaxDivulgaController::class, 'themeStudio'])->name('lojista.maxdivulga.themes');
        Route::get('/themes/preview', [MaxDivulgaController::class, 'themePreview'])->name('lojista.maxdivulga.theme_preview');

        // Editor & Builder — somente admin e super-admin
        Route::middleware(['role:admin|super-admin'])->group(function () {
            Route::post('/themes/render-code', [MaxDivulgaController::class, 'themeRenderCode'])->name('lojista.maxdivulga.theme_render_code');
            Route::get('/themes/{theme}/editor', [MaxDivulgaController::class, 'themeEditor'])->name('lojista.maxdivulga.theme_editor');
            Route::post('/themes/{theme}/save', [MaxDivulgaController::class, 'themeEditorSave'])->name('lojista.maxdivulga.theme_editor_save');
            Route::get('/themes/{theme}/builder', [MaxDivulgaController::class, 'themeBuilder'])->name('lojista.maxdivulga.theme_builder');
            Route::get('/themes/{theme}/builder-frame', [MaxDivulgaController::class, 'themeBuilderFrame'])->name('lojista.maxdivulga.theme_builder_frame');
            Route::post('/themes/{theme}/builder-save', [MaxDivulgaController::class, 'themeBuilderSave'])->name('lojista.maxdivulga.theme_builder_save');
        });


        // Canais Sociais
        Route::prefix('canais')->group(function () {
            Route::get('/', [MaxDivulgaController::class, 'canaisIndex'])->name('lojista.maxdivulga.canais.index');
            Route::get('/auth/{provider}', [\App\Http\Controllers\Lojista\SocialAuthController::class, 'redirectToProvider'])->name('lojista.maxdivulga.canais.auth');
            Route::get('/callback/{provider}', [\App\Http\Controllers\Lojista\SocialAuthController::class, 'handleProviderCallback'])->name('facebook.callback');
            Route::post('/telegram', [\App\Http\Controllers\Lojista\SocialAuthController::class, 'connectTelegram'])->name('lojista.maxdivulga.canais.telegram');
            Route::delete('/{provider}', [\App\Http\Controllers\Lojista\SocialAuthController::class, 'disconnect'])->name('lojista.maxdivulga.canais.disconnect');
            Route::post('/publish/{campaign}', [\App\Http\Controllers\Lojista\SocialAuthController::class, 'publish'])->name('lojista.maxdivulga.canais.publish');
        });
    });
});

// Retornos de Pagamento MP
Route::get('/pagamentos/sucesso', [\App\Http\Controllers\PagamentosController::class, 'sucesso'])->name('pagamentos.sucesso');
Route::get('/pagamentos/falha', [\App\Http\Controllers\PagamentosController::class, 'falha'])->name('pagamentos.falha');
Route::get('/pagamentos/pendente', [\App\Http\Controllers\PagamentosController::class, 'pendente'])->name('pagamentos.pendente');

Route::get('/limpar-tudo', function () {
    $user = auth()->user();

    // 1. Limpa o Cache Específico do Spatie (CRUCIAL)
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    // 2. Limpa o Cache Geral do Laravel
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    // 3. Recarrega o usuário do banco para garantir
    if ($user) {
        $user->refresh();
    }

    // 4. Mostra o resultado na tela
    return response()->json([
        'status' => 'Caches limpos com sucesso!',
        'usuario' => $user ? $user->email : 'Não logado',
        'roles_atuais' => $user ? $user->getRoleNames() : [],
        'e_admin_pelo_spatie' => $user ? $user->hasRole('admin') : false,
    ]);
});