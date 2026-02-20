@extends('layouts.user_type.auth')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Minhas Campanhas - MaxDivulga</h5>
                            <p class="text-sm mb-0">Gerencie seus catálogos e publicações inteligentes criados com IA.</p>
                        </div>
                        <a href="{{ route('lojista.maxdivulga.create') }}" class="btn bg-gradient-primary btn-sm mb-0"
                            type="button">+ Nova Campanha AI</a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2 mt-3">
                    @if(session('success'))
                        <div class="alert alert-success mx-4" role="alert">
                            <span class="text-white">{{ session('success') }}</span>
                        </div>
                    @endif
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Campanha
                                    </th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Formato</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Canais</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Status</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaigns as $camp)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $camp->name }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ ucfirst($camp->type) }} |
                                                        {{ $camp->schedule_type == 'unique' ? 'Única' : 'Recorrente' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span
                                                class="badge badge-sm bg-gradient-info">{{ strtoupper($camp->format ?? 'ND') }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            @foreach($camp->channels ?? [] as $channel)
                                                <span class="badge badge-sm bg-gradient-secondary">{{ ucfirst($channel) }}</span>
                                            @endforeach
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            @if($camp->status == 'active')
                                                <span class="badge badge-sm bg-gradient-success">Ativa</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-warning">{{ ucfirst($camp->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('lojista.maxdivulga.show', $camp->id) }}" class="text-primary font-weight-bold text-xs mx-1" data-toggle="tooltip" data-original-title="Visualizar">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                            <a href="{{ route('lojista.maxdivulga.edit', $camp->id) }}" class="text-info font-weight-bold text-xs mx-1" data-toggle="tooltip" data-original-title="Editar">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <a href="{{ route('lojista.maxdivulga.download', $camp->id) }}" class="text-secondary font-weight-bold text-xs mx-1" data-toggle="tooltip" data-original-title="Baixar">
                                                <i class="fas fa-download"></i> Baixar
                                            </a>
                                            <a href="#" class="text-danger font-weight-bold text-xs mx-1" data-toggle="tooltip" data-original-title="Excluir" onclick="event.preventDefault(); if(confirm('Tem certeza que deseja apagar esta campanha?')) document.getElementById('delete-form-{{ $camp->id }}').submit();">
                                                <i class="fas fa-trash"></i> Excluir
                                            </a>
                                            <form id="delete-form-{{ $camp->id }}" action="{{ route('lojista.maxdivulga.destroy', $camp->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-sm py-4">Você ainda não criou nenhuma campanha
                                            MaxDivulga.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection