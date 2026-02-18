@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">

      @if(session('status'))
        <div class="alert alert-success text-white" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
        </div>
      @endif
      
      @if(session('error'))
        <div class="alert alert-danger text-white" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger text-white">
            <ul>
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
      @endif

      <div class="card mb-4">
        <div class="card-body p-3">
            <form action="{{ route('funcionarios.index') }}" method="GET" class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="mb-0">Gerenciar Equipe</h6>
                    <p class="text-sm mb-0">Selecione a loja para visualizar</p>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-store"></i></span>
                        <select class="form-select" name="loja" onchange="this.form.submit()">
                            @if($allLojas->isEmpty())
                                <option value="">Você não possui lojas</option>
                            @endif
                            
                            @foreach($allLojas as $optionLoja)
                                <option value="{{ $optionLoja->codigo }}" 
                                    {{ (isset($codigoSelecionado) && $codigoSelecionado == $optionLoja->codigo) ? 'selected' : '' }}>
                                    {{ $optionLoja->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
      </div>

      @if($lojas->isEmpty())
        <div class="alert alert-light text-center border">
            <i class="fas fa-info-circle fa-2x mb-3 d-block text-secondary"></i>
            Nenhuma loja selecionada ou encontrada.
        </div>
      @else
        @foreach($lojas as $loja) <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <h6>Lojа: {{ $loja->nome }} <small class="text-muted">(@if($loja->user) Proprietário: {{ $loja->user->name }} @endif)</small></h6>
            
            <button type="button" class="btn bg-gradient-primary btn-sm mb-0 add-perm-btn" 
                data-bs-toggle="modal" data-bs-target="#addPermModal" data-loja-codigo="{{ $loja->codigo }}">
                <i class="fas fa-plus me-2"></i> Adicionar
            </button>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Usuário</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Cargo</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Loja</th>
                    <th class="text-secondary opacity-7">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loja->permissoes as $perm)
                    @php
                    $perUser = $perm->user;
                    $img = $perUser && $perUser->codigo ? asset('storage/usuario/' . $perUser->codigo . '/perfil.jpg') : asset('/assets/img/team-2.jpg');
                    $userData = json_encode([
                        'name' => $perUser->name ?? 'N/A',
                        'email' => $perUser->email ?? 'N/A',
                        'phone' => $perUser->phone ?? 'Não informado',
                        'location' => $perUser->location ?? 'Não informado',
                        'about' => $perUser->about ?? 'Sem descrição',
                        'created_at' => $perUser->created_at ? $perUser->created_at->format('d/m/Y') : '-'
                    ]);
                    @endphp
                    <tr>
                    <td>
                        <div class="d-flex px-2 py-1">
                        <div><img src="{{ $img }}" class="avatar avatar-sm me-3" alt="user"></div>
                        <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ $perUser ? $perUser->name : 'Usuário Desconhecido' }}</h6>
                            <p class="text-xs text-secondary mb-0">{{ $perUser ? $perUser->email : '' }}</p>
                        </div>
                        </div>
                    </td>
                    <td class="align-middle text-sm">
                        <span class="badge badge-sm bg-gradient-info">{{ $perm->role ?? 'Funcionário' }}</span>
                    </td>
                    <td class="align-middle text-center">
                        <span class="text-xs font-weight-bold">{{ $loja->nome }}</span>
                    </td>
                    <td class="align-middle">
                        
                        <a href="javascript:;" class="btn btn-link text-dark px-2 mb-0 view-user-btn" 
                           data-bs-toggle="modal" data-bs-target="#viewUserModal"
                           data-user="{{ $userData }}" data-img="{{ $img }}" title="Ver">
                           <i class="fas fa-eye text-dark me-1"></i>
                        </a>

                        <a href="javascript:;" class="btn btn-link text-dark px-2 mb-0 edit-perm-btn" 
                           data-bs-toggle="modal" data-bs-target="#editPermModal"
                           data-per-id="{{ $perm->id }}" 
                           data-loja-codigo="{{ $loja->codigo }}" 
                           data-rol="{{ $perm->role ?? '' }}" title="Editar">
                           <i class="fas fa-pencil-alt text-dark me-1"></i>
                        </a>

                        <form action="{{ route('lojas.permissoes.destroy', ['lojaCodigo' => $loja->codigo, 'permissao' => $perm->id]) }}" 
                            method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-link text-danger px-2 mb-0 delete-btn" title="Excluir">
                                <i class="fas fa-trash text-danger me-1"></i>
                            </button>
                        </form>

                    </td>
                    </tr>
                    @endforeach
                    @if($loja->permissoes->isEmpty())
                    <tr><td colspan="4" class="text-center text-sm py-4">Nenhum funcionário cadastrado nesta loja.</td></tr>
                    @endif
                </tbody>
                </table>
            </div>
            </div>
        </div>
        @endforeach
      @endif
    </div>
  </main>

  <div class="modal fade" id="addPermModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <form id="addPermForm" method="POST" action="">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Adicionar Funcionário</h5>
            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group mb-3">
              <label>E-mail do Usuário</label>
              <input type="email" class="form-control" name="email" required placeholder="email@usuario.com">
            </div>
            <div class="form-group">
              <label>Permissão</label>
              <select class="form-control" name="role">
                  <option value="Vendedor">Vendedor</option>
                  <option value="Gerente">Gerente</option>
                  <option value="Estoquista">Estoquista</option>
                  <option value="Caixa">Caixa</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn bg-gradient-primary">Adicionar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editPermModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <form id="editPermForm" method="POST" action="#">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title">Editar Permissão</h5>
            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Permissão</label>
              <select class="form-control" id="role_edit" name="role">
                  <option value="Vendedor">Vendedor</option>
                  <option value="Gerente">Gerente</option>
                  <option value="Estoquista">Estoquista</option>
                  <option value="Caixa">Caixa</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn bg-gradient-primary">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="viewUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Dados do Funcionário</h5>
          <button type="button" class="btn-close text-dark" data-bs-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body text-center">
            <img src="" id="view_img" class="avatar avatar-xl mb-3 border-radius-lg shadow-sm">
            <h5 id="view_name" class="font-weight-bolder"></h5>
            <p id="view_email" class="text-secondary mb-4"></p>
            <div class="row text-start px-3">
                <div class="col-12 mb-2"><strong>Telefone:</strong> <span id="view_phone"></span></div>
                <div class="col-12 mb-2"><strong>Localização:</strong> <span id="view_location"></span></div>
                <div class="col-12 mb-2"><strong>Desde:</strong> <span id="view_created"></span></div>
                <div class="col-12 mt-2"><strong>Sobre:</strong> <p id="view_about" class="text-sm border p-2 mt-1"></p></div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  
  // 1. MODAL ADICIONAR
  const addBtns = document.querySelectorAll('.add-perm-btn');
  addBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        const lojaCodigo = this.getAttribute('data-loja-codigo');
        const form = document.getElementById('addPermForm');
        form.action = '/lojas/' + lojaCodigo + '/funcionarios';
    });
  });

  // 2. MODAL EDITAR
  const editBtns = document.querySelectorAll('.edit-perm-btn');
  editBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        const permId = this.getAttribute('data-per-id');
        const lojaCodigo = this.getAttribute('data-loja-codigo');
        const currentRole = this.getAttribute('data-rol');
        
        const form = document.getElementById('editPermForm');
        const roleSelect = document.getElementById('role_edit');
        
        form.action = '/lojas/' + lojaCodigo + '/permissoes/' + permId;
        roleSelect.value = currentRole;
    });
  });

  // 3. MODAL VISUALIZAR
  const viewBtns = document.querySelectorAll('.view-user-btn');
  viewBtns.forEach(btn => {
      btn.addEventListener('click', function() {
          const userData = JSON.parse(this.getAttribute('data-user'));
          const imgUrl = this.getAttribute('data-img');
          document.getElementById('view_img').src = imgUrl;
          document.getElementById('view_name').innerText = userData.name;
          document.getElementById('view_email').innerText = userData.email;
          document.getElementById('view_phone').innerText = userData.phone;
          document.getElementById('view_location').innerText = userData.location;
          document.getElementById('view_created').innerText = userData.created_at;
          document.getElementById('view_about').innerText = userData.about;
      });
  });

  // 4. SWEETALERT EXCLUIR
  const deleteBtns = document.querySelectorAll('.delete-btn');
  deleteBtns.forEach(btn => {
      btn.addEventListener('click', function() {
          const form = this.closest('form');
          Swal.fire({
              title: 'Tem certeza?',
              text: "Remover acesso deste funcionário?",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#ea0606',
              cancelButtonColor: '#828282',
              confirmButtonText: 'Sim, remover!',
              cancelButtonText: 'Cancelar'
          }).then((result) => {
              if (result.isConfirmed) form.submit();
          })
      });
  });

});
</script>
@endpush