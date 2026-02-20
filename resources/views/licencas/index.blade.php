@extends('layouts.user_type.auth')

@section('content')
    <div class="container">
        <h2>Licenças</h2>
        <a href="{{ route('licencas.create') }}" class="btn btn-primary mb-3">Nova Licença</a>

        <div class="table-responsive">
            <table class="table table-bordered align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cód</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Key</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Descrição</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Validade
                        </th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status
                        </th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Loja
                        </th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Ações
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($licencas as $licenca)
                        <tr>
                            <td class="align-middle text-sm font-weight-bold">
                                #{{ $licenca->codigo }}
                            </td>
                            <td class="align-middle text-sm">
                                <div class="d-flex align-items-center">
                                    <span class="font-weight-bold me-2" style="font-family: monospace;">********{{ substr($licenca->key, -4) }}</span>
                                    <button type="button" class="btn btn-link text-secondary mb-0 px-0" 
                                        onclick="copyToClipboard('{{ $licenca->key }}')" 
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Copiar Key Inteira">
                                        <i class="far fa-copy text-sm"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="align-middle text-sm">{{ $licenca->descricao }}</td>
                            <td class="align-middle text-center text-sm">
                                {{ \Carbon\Carbon::parse($licenca->validade)->format('d/m/Y') }}</td>
                            <td class="align-middle text-center text-sm">
                                <span
                                    class="badge badge-sm {{ $licenca->status == 'ativo' ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                                    {{ $licenca->status == 'ativo' ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td class="align-middle text-center text-sm">{{ $licenca->loja->nome }}</td>
                            <td class="align-middle text-center" style="white-space: nowrap;">
                                <a href="{{ route('licencas.edit', $licenca->codigo) }}"
                                    class="btn btn-icon-only btn-rounded btn-outline-warning mb-0 me-1"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('pagamentos.faturas', ['loja_codigo' => $licenca->loja->codigo ?? '']) }}"
                                    class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-1"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Faturas Financeiras">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </a>
                                <a href="{{ route('assinaturas.index', $licenca->id) }}"
                                    class="btn btn-icon-only btn-rounded btn-outline-primary mb-0 me-1"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Mudar Plano ou Extras">
                                    <i class="fas fa-shopping-cart"></i>
                                </a>
                                <form action="{{ route('licencas.destroy', $licenca->codigo) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon-only btn-rounded btn-outline-danger mb-0"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Excluir"
                                        onclick="return confirm('Deseja realmente excluir esta licença?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Script de Cópia e Init Tooltips -->
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Chave copiada com sucesso: ' + text);
            });
        }
        
        // Assegura que os tooltips estão ativados logo após renderizar a tabela
        document.addEventListener("DOMContentLoaded", function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
@endsection