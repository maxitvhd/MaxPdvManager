@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4 mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="row">
                            
                            <div class="col-md-5 text-center mb-4 mb-md-0 d-flex flex-column align-items-center justify-content-center">
                                @if ($produto->imagem)
                                    <img src="{{ asset('storage/lojas/' . ($produto->loja->codigo ?? Auth::user()->codigo) . '/produtos/' . $produto->imagem) }}" 
                                         alt="{{ $produto->nome }}" 
                                         class="img-fluid rounded-3 shadow-sm" 
                                         style="max-height: 400px; width: 100%; object-fit: contain;"
                                         onerror="this.onerror=null; this.src='{{ asset('storage/produtos_full/' . $produto->imagem) }}'; if(this.src=='{{ asset('storage/produtos_full/' . $produto->imagem) }}') this.src='https://via.placeholder.com/400x400.png?text=Sem+Imagem';">
                                @else
                                    <div class="bg-light rounded-3 d-flex flex-column align-items-center justify-content-center shadow-sm" style="height: 350px; width: 100%;">
                                        <i class="fas fa-box-open fa-4x text-secondary mb-3"></i>
                                        <span class="text-secondary font-weight-bold">Sem Imagem</span>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-7 ps-md-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h2 class="font-weight-bolder text-dark mb-0">{{ $produto->nome }}</h2>
                                    <span class="badge bg-primary text-white rounded-pill px-3 py-2" style="font-size: 0.85rem;">
                                        {{ $produto->categoria ?? 'Geral' }}
                                    </span>
                                </div>
                                <p class="text-muted text-sm mb-4">
                                    Código de Barras (EAN): <span class="font-weight-bold text-dark">{{ $produto->codigo_barra }}</span>
                                </p>

                                <div class="mb-4 p-3 bg-light rounded-3">
                                    <h2 class="text-success font-weight-bolder mb-0">
                                        R$ {{ number_format($produto->preco, 2, ',', '.') }} 
                                        <span class="text-sm text-muted font-weight-normal">/ venda</span>
                                    </h2>
                                    <p class="text-muted text-sm mt-1 mb-0">
                                        Preço de Custo: R$ {{ number_format($produto->preco_compra, 2, ',', '.') }}
                                    </p>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon text-center rounded-circle p-2 me-2 bg-gradient-primary shadow text-white" style="width: 40px; height: 40px;">
                                                <i class="fas fa-boxes mt-1"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-muted mb-0">Estoque Atual</p>
                                                <h6 class="font-weight-bolder mb-0">{{ $produto->estoque }} un</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon text-center rounded-circle p-2 me-2 bg-gradient-primary shadow text-white" style="width: 40px; height: 40px;">
                                                <i class="fas fa-weight-hanging mt-1"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-muted mb-0">Peso</p>
                                                <h6 class="font-weight-bolder mb-0">{{ $produto->peso }} kg</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon text-center rounded-circle p-2 me-2 bg-gradient-primary shadow text-white" style="width: 40px; height: 40px;">
                                                <i class="fas fa-calendar-alt mt-1"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-muted mb-0">Validade</p>
                                                <h6 class="font-weight-bolder mb-0">{{ $produto->validade ? date('d/m/Y', strtotime($produto->validade)) : 'N/A' }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon text-center rounded-circle p-2 me-2 bg-gradient-primary shadow text-white" style="width: 40px; height: 40px;">
                                                <i class="fas fa-file-invoice-dollar mt-1"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-muted mb-0">NFe (NCM/CEST)</p>
                                                <h6 class="font-weight-bolder mb-0">{{ $produto->codigo_nfe ?? 'N/A' }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="horizontal dark mt-3 mb-3">
                                
                                <div class="mb-4">
                                    <h6 class="font-weight-bolder">Descrição do Produto</h6>
                                    <p class="text-sm text-muted" style="line-height: 1.6;">
                                        {{ $produto->descricao ?? 'Nenhuma descrição detalhada foi fornecida para este produto no momento do cadastro.' }}
                                    </p>
                                </div>

                                <div class="d-flex align-items-center mt-4">
                                    <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary me-3 px-4">
                                        <i class="fas fa-arrow-left me-2"></i> Voltar
                                    </a>
                                </div>
                                <div class="mt-2 text-xs text-muted">
                                    Responsável pelo cadastro: <strong>{{ $produto->user->name ?? 'Não informado' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection