@extends('layouts.user_type.auth')

@section('content')
  @php
      $user = auth()->user();
      $socials = json_decode($user->about_me, true) ?: [];
      $aboutText = $socials['about_text'] ?? '';
      $facebook = $socials['facebook'] ?? '';
      $twitter = $socials['twitter'] ?? '';
      $instagram = $socials['instagram'] ?? '';
      $linkedin = $socials['linkedin'] ?? '';
  @endphp

  <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
    <div class="container-fluid">
      <div class="page-header min-height-300 border-radius-xl mt-4" style="background-image: url('../assets/img/curved-images/curved0.jpg'); background-position-y: 50%;">
        <span class="mask bg-gradient-primary opacity-6"></span>
      </div>
      <div class="card card-body blur shadow-blur mx-4 mt-n6 overflow-hidden">
        <div class="row gx-4">
          <div class="col-auto">
            <div class="avatar avatar-xl position-relative">
              <img src="{{ $user->imagem ? asset($user->imagem) : '../assets/img/bruce-mars.jpg' }}" alt="profile_image" class="w-100 border-radius-lg shadow-sm" style="object-fit: cover; aspect-ratio: 1/1;">
            </div>
          </div>
          <div class="col-auto my-auto">
            <div class="h-100">
              <h5 class="mb-1">
                {{ $user->name }}
              </h5>
              <p class="mb-0 font-weight-bold text-sm">
                {{ $user->funcao ?? 'Usuário' }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="container-fluid py-4">
      <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">{{ __('Editar Perfil') }}</h6>
        </div>
        <div class="card-body pt-4 p-3">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                @if($errors->any())
                    <div class="mt-3 alert alert-primary alert-dismissible fade show" role="alert">
                        <span class="alert-text text-white">{{$errors->first()}}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                @endif
                @if(session('success'))
                    <div class="m-3 alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
                        <span class="alert-text text-white">{{ session('success') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <!-- Informações Básicas -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user-name" class="form-control-label">{{ __('Nome Completo') }}</label>
                            <input class="form-control" value="{{ old('name', $user->name) }}" type="text" placeholder="Nome" id="user-name" name="name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user-email" class="form-control-label">{{ __('Email') }}</label>
                            <input class="form-control" value="{{ old('email', $user->email) }}" type="email" placeholder="@exemplo.com" id="user-email" name="email" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="user-phone" class="form-control-label">{{ __('Telefone') }}</label>
                            <input class="form-control" value="{{ old('phone', $user->phone) }}" type="tel" placeholder="Telefone" id="user-phone" name="phone">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="user-funcao" class="form-control-label">{{ __('Função') }} <small>(Apenas leitura)</small></label>
                            <input class="form-control" value="{{ $user->funcao }}" type="text" id="user-funcao" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="user-imagem" class="form-control-label">{{ __('Foto de Perfil') }}</label>
                            <input class="form-control" type="file" id="user-imagem" name="imagem" accept="image/*">
                        </div>
                    </div>
                </div>

                <hr class="horizontal dark">
                <h6 class="mb-3">Endereço</h6>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cep" class="form-control-label">{{ __('CEP') }}</label>
                            <input class="form-control" value="{{ old('cep', $user->cep) }}" type="text" id="cep" name="cep">
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <label for="endereco" class="form-control-label">{{ __('Endereço') }}</label>
                            <input class="form-control" value="{{ old('endereco', $user->endereco) }}" type="text" id="endereco" name="endereco">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="bairro" class="form-control-label">{{ __('Bairro') }}</label>
                            <input class="form-control" value="{{ old('bairro', $user->bairro) }}" type="text" id="bairro" name="bairro">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="cidade" class="form-control-label">{{ __('Cidade') }}</label>
                            <input class="form-control" value="{{ old('cidade', $user->cidade) }}" type="text" id="cidade" name="cidade">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="estado" class="form-control-label">{{ __('Estado') }}</label>
                            <input class="form-control" value="{{ old('estado', $user->estado) }}" type="text" id="estado" name="estado" maxlength="2">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="location" class="form-control-label">{{ __('Localização Adicional') }}</label>
                            <input class="form-control" value="{{ old('location', $user->location) }}" type="text" id="location" name="location">
                        </div>
                    </div>
                </div>

                <hr class="horizontal dark">
                <h6 class="mb-3">Redes Sociais & Sobre</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="facebook" class="form-control-label">{{ __('Facebook (URL)') }}</label>
                            <input class="form-control" value="{{ old('facebook', $facebook) }}" type="url" id="facebook" name="facebook">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="instagram" class="form-control-label">{{ __('Instagram (URL)') }}</label>
                            <input class="form-control" value="{{ old('instagram', $instagram) }}" type="url" id="instagram" name="instagram">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="twitter" class="form-control-label">{{ __('Twitter (URL)') }}</label>
                            <input class="form-control" value="{{ old('twitter', $twitter) }}" type="url" id="twitter" name="twitter">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="linkedin" class="form-control-label">{{ __('LinkedIn (URL)') }}</label>
                            <input class="form-control" value="{{ old('linkedin', $linkedin) }}" type="url" id="linkedin" name="linkedin">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="about_text">{{ __('Sobre Mim') }}</label>
                    <textarea class="form-control" id="about_text" rows="3" name="about_text">{{ old('about_text', $aboutText) }}</textarea>
                </div>

                <hr class="horizontal dark">
                <h6 class="mb-3">Sessão e IP</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ip" class="form-control-label">{{ __('Último IP') }} <small>(Apenas leitura)</small></label>
                            <input class="form-control" value="{{ $user->ip }}" type="text" id="ip" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="geolocalizacao_display" class="form-control-label">{{ __('Geolocalização') }} <small>(Atualizada automaticamente)</small></label>
                            <input class="form-control" value="{{ $user->geolocalizacao }}" type="text" id="geolocalizacao_display" disabled>
                            
                            <!-- Hidden input to actual submit -->
                            <input type="hidden" name="geolocalizacao" id="geolocalizacao_input" value="{{ $user->geolocalizacao }}">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">{{ __('Salvar Alterações') }}</button>
                </div>
            </form>
        </div>
      </div>
      @include('layouts.footers.auth.footer')
    </div>
  </div>

  <script>
    // Get current geolocation if possible and update the hidden input
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const geoString = lat + ', ' + lng;
            document.getElementById('geolocalizacao_input').value = geoString;
            document.getElementById('geolocalizacao_display').value = geoString;
        });
    }
  </script>
@endsection
