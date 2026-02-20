@extends('layouts.user_type.auth')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">MaxDivulga - Configuração Master</h5>
                            <p class="text-sm">Configure as chaves e modelos de IA e TTS.</p>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4 pt-4 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success mt-2">{{ session('success') }}</div>
                    @endif
                    <form action="{{ route('admin.maxdivulga.store_config') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Inteligência Artificial (Textos e Gatilhos)</h6>
                                <div class="form-group mb-3">
                                    <label for="provider_ia">Provedor IA</label>
                                    <select name="provider_ia" id="provider_ia" class="form-control">
                                        <option value="openai" {{ ($config->provider_ia ?? '') == 'openai' ? 'selected' : '' }}>OpenAI</option>
                                        <option value="gemini" {{ ($config->provider_ia ?? '') == 'gemini' ? 'selected' : '' }}>Gemini (Google)</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="api_key_ia">API Key IA</label>
                                    <input type="text" name="api_key_ia" id="api_key_ia" class="form-control"
                                        value="{{ $config->api_key_ia ?? '' }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="model_ia">Modelo IA (ex: gpt-4o-mini, gemini-1.5-flash)</label>
                                    <input type="text" name="model_ia" id="model_ia" class="form-control"
                                        value="{{ $config->model_ia ?? '' }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6>Text-to-Speech (Geração de Áudios)</h6>
                                <div class="form-group mb-3">
                                    <label for="provider_tts">Servidor / API TTS</label>
                                    <input type="text" name="provider_tts" id="provider_tts" class="form-control"
                                        value="{{ $config->provider_tts ?? '' }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="tts_host">Host (IP) do TTS</label>
                                    <input type="text" name="tts_host" id="tts_host" class="form-control"
                                        value="{{ $config->tts_host ?? '' }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="tts_api_key">API Key TTS</label>
                                    <input type="text" name="tts_api_key" id="tts_api_key" class="form-control"
                                        value="{{ $config->tts_api_key ?? '' }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="tts_model">Modelo/Voz TTS</label>
                                    <input type="text" name="tts_model" id="tts_model" class="form-control"
                                        value="{{ $config->tts_model ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn bg-gradient-dark btn-sm mt-3">Salvar Configurações</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection