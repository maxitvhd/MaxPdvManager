@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card h-100 mb-4 bg-gradient-dark">
                <div class="card-header pb-0 bg-transparent">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="text-white mb-0">Módulo TvDoor - Digital Signage</h5>
                            <p class="text-white text-sm opacity-8">Gerencie suas telas, mídias e agendamentos de forma profissional.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('lojista.tvdoor.players.index') }}" class="btn btn-outline-white btn-sm mb-0">Gerenciar Players</a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row mt-4">
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card bg-transparent border border-secondary shadow-none">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="numbers">
                                                <p class="text-white text-sm mb-0 text-capitalize font-weight-bold opacity-7">Players Ativos</p>
                                                <h5 class="text-white font-weight-bolder mb-0">
                                                    {{ $playersCount }}
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center border-radius-md">
                                                <i class="ni ni-tv-2 text-lg opacity-10" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card bg-transparent border border-secondary shadow-none">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="numbers">
                                                <p class="text-white text-sm mb-0 text-capitalize font-weight-bold opacity-7">Biblioteca de Mídias</p>
                                                <h5 class="text-white font-weight-bolder mb-0">
                                                    {{ $mediaCount }}
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="icon icon-shape bg-gradient-success shadow-success text-center border-radius-md">
                                                <i class="ni ni-album-2 text-lg opacity-10" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card bg-transparent border border-secondary shadow-none">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="numbers">
                                                <p class="text-white text-sm mb-0 text-capitalize font-weight-bold opacity-7">Layouts Criados</p>
                                                <h5 class="text-white font-weight-bolder mb-0">
                                                    {{ $layoutsCount }}
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="icon icon-shape bg-gradient-info shadow-info text-center border-radius-md">
                                                <i class="ni ni-palette text-lg opacity-10" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="card bg-transparent border border-secondary shadow-none">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="numbers">
                                                <p class="text-white text-sm mb-0 text-capitalize font-weight-bold opacity-7">Integração</p>
                                                <h5 class="text-white font-weight-bolder mb-0">
                                                    Ativa
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center border-radius-md">
                                                <i class="ni ni-spaceship text-lg opacity-10" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-4 mb-lg-0 mb-4">
            <div class="card h-100">
                <div class="card-body p-3">
                    <div class="bg-gradient-primary border-radius-lg h-100">
                        <img src="{{ asset('assets/img/shapes/waves-white.svg') }}" class="position-absolute h-100 w-50 top-0 d-lg-block d-none" alt="waves">
                        <div class="position-relative d-flex align-items-center justify-content-center h-100">
                            <img class="w-100 position-relative z-index-2 pt-4" src="{{ asset('assets/img/illustrations/rocket-white.png') }}" alt="rocket">
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <h5 class="font-weight-bolder">Passo 1: Conecte seu Player</h5>
                    <p class="text-sm">Cadastre um novo dispositivo e use o código de pareamento no seu player HTML ou Python.</p>
                    <a href="{{ route('lojista.tvdoor.players.index') }}" class="btn btn-primary btn-sm mb-0">Ir para Players</a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-lg-0 mb-4">
            <div class="card h-100">
                <div class="card-body p-3 text-center">
                    <div class="icon icon-shape icon-lg bg-gradient-info shadow text-center border-radius-lg mb-3">
                        <i class="ni ni-palette text-white opacity-10"></i>
                    </div>
                    <h5 class="font-weight-bolder">Passo 2: Crie seu Layout</h5>
                    <p class="text-sm">Use nosso editor estilo Canva para montar suas telas com produtos do catálogo, relógios e mídias.</p>
                    <a href="{{ route('lojista.tvdoor.layouts.create') }}" class="btn btn-info btn-sm mb-0">Criar Layout</a>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body p-3 text-center">
                    <div class="icon icon-shape icon-lg bg-gradient-success shadow text-center border-radius-lg mb-3">
                        <i class="ni ni-calendar-grid-58 text-white opacity-10"></i>
                    </div>
                    <h5 class="font-weight-bolder">Passo 3: Agende suas Exibições</h5>
                    <p class="text-sm">Defina datas e horários para que seus layouts e mídias sejam exibidos nos players selecionados.</p>
                    <a href="{{ route('lojista.tvdoor.schedules.index') }}" class="btn btn-success btn-sm mb-0">Agendar Agora</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
