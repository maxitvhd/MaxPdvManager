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
                                    <label for="tts_model">Modelo/Voz TTS (Padrão)</label>
                                    <input type="text" name="tts_model" id="tts_model" class="form-control"
                                        value="{{ $config->tts_model ?? 'pt-BR-FabioNeural' }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="tts_default_speed">Velocidade Padrão (Speed)</label>
                                    <input type="number" step="0.05" name="tts_default_speed" id="tts_default_speed"
                                        class="form-control" value="{{ $config->tts_default_speed ?? '1.25' }}">
                                </div>
                                <div class="row">
                                    <div class="col-6 form-group mb-3">
                                        <label for="tts_default_noise_scale">Emoção (Noise Scale)</label>
                                        <input type="number" step="0.001" name="tts_default_noise_scale"
                                            id="tts_default_noise_scale" class="form-control"
                                            value="{{ $config->tts_default_noise_scale ?? '0.750' }}">
                                    </div>
                                    <div class="col-6 form-group mb-3">
                                        <label for="tts_default_noise_w">Dicção (Noise W)</label>
                                        <input type="number" step="0.001" name="tts_default_noise_w"
                                            id="tts_default_noise_w" class="form-control"
                                            value="{{ $config->tts_default_noise_w ?? '0.850' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="horizontal dark my-4">

                        <div class="row">
                            <div class="col-md-6">
                                <h6>Integração Social Facebook (Páginas e Grupos)</h6>
                                <p class="text-xs text-muted">Obtenha estas chaves no console do <a
                                        href="https://developers.facebook.com" target="_blank">Facebook Developers</a></p>
                                <div class="form-group mb-3">
                                    <label for="facebook_client_id">Facebook App ID (Client ID)</label>
                                    <input type="text" name="facebook_client_id" id="facebook_client_id"
                                        class="form-control" value="{{ $config->facebook_client_id ?? '' }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="facebook_client_secret">Facebook App Secret (Client Secret)</label>
                                    <input type="password" name="facebook_client_secret" id="facebook_client_secret"
                                        class="form-control" value="{{ $config->facebook_client_secret ?? '' }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6>Integração Social Google (YouTube/Google My Business)</h6>
                                <p class="text-xs text-muted">Configuração para futuras automações.</p>
                                <div class="form-group mb-3">
                                    <label for="google_client_id">Google Client ID</label>
                                    <input type="text" name="google_client_id" id="google_client_id" class="form-control"
                                        value="{{ $config->google_client_id ?? '' }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="google_client_secret">Google Client Secret</label>
                                    <input type="password" name="google_client_secret" id="google_client_secret"
                                        class="form-control" value="{{ $config->google_client_secret ?? '' }}">
                                </div>
                            </div>

                            <div class="col-md-12 mt-3">
                                <h6>Integração Telegram (Bot de Envio)</h6>
                                <p class="text-xs text-muted">Crie um bot no <a href="https://t.me/BotFather"
                                        target="_blank">@BotFather</a> e cole o Token aqui.</p>
                                <div class="form-group mb-3">
                                    <label for="telegram_bot_token">Telegram Bot Token (API Key)</label>
                                    <input type="text" name="telegram_bot_token" id="telegram_bot_token"
                                        class="form-control" value="{{ $config->telegram_bot_token ?? '' }}"
                                        placeholder="Ex: 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11">
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