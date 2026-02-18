@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">

      @if($errors->any())
        <div class="alert alert-danger text-white" role="alert">
            <h6 class="text-white">Ops! Verifique os campos:</h6>
            <ul class="mb-0 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif

      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center">
                  <h5><i class="fas fa-edit me-2"></i>Editar Loja: {{ $loja->nome }}</h5>
                  <a href="{{ route('lojas.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                  </a>
              </div>
            </div>
            
            <div class="card-body">
              <form action="{{ route('lojas.update', $loja->codigo) }}" method="POST">
                @csrf
                @method('PUT')
                
                <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Informações Gerais</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome" class="form-control-label">Nome da Loja <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="nome" id="nome" value="{{ old('nome', $loja->nome) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="form-control-label">E-mail Comercial <span class="text-danger">*</span></label>
                            <input class="form-control" type="email" name="email" id="email" value="{{ old('email', $loja->email) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="telefone" class="form-control-label">Telefone <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="telefone" id="telefone" value="{{ old('telefone', $loja->telefone) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status" class="form-control-label">Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" id="status" required>
                                <option value="ativo" {{ (old('status', $loja->status) == 'ativo' || old('status', $loja->status) == 1) ? 'selected' : '' }}>Ativa</option>
                                <option value="inativo" {{ (old('status', $loja->status) == 'inativo' || old('status', $loja->status) == 0) ? 'selected' : '' }}>Inativa</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="horizontal dark my-3">

                <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Documentação</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cnpj" class="form-control-label">CNPJ <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="cnpj" id="cnpj" value="{{ old('cnpj', $loja->cnpj) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cpf" class="form-control-label">CPF do Responsável <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="cpf" id="cpf" value="{{ old('cpf', $loja->cpf) }}" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="descricao" class="form-control-label">Descrição</label>
                            <textarea class="form-control" name="descricao" id="descricao" rows="3" required>{{ old('descricao', $loja->descricao) }}</textarea>
                        </div>
                    </div>
                </div>

                <hr class="horizontal dark my-3">

                <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Endereço</h6>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cep" class="form-control-label">CEP <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="cep" id="cep" value="{{ old('cep', $loja->cep) }}" required onblur="buscarCep(this.value)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="endereco" class="form-control-label">Logradouro <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="endereco" id="endereco" value="{{ old('endereco', $loja->endereco) }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="bairro" class="form-control-label">Bairro <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="bairro" id="bairro" value="{{ old('bairro', $loja->bairro) }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="cidade" class="form-control-label">Cidade <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="cidade" id="cidade" value="{{ old('cidade', $loja->cidade) }}" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="estado" class="form-control-label">Estado (UF) <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="estado" id="estado" maxlength="2" value="{{ old('estado', $loja->estado) }}" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('lojas.index') }}" class="btn btn-light m-0">Cancelar</a>
                    <button type="submit" class="btn bg-gradient-primary m-0 ms-2">
                        <i class="fas fa-save me-2"></i> Atualizar Dados
                    </button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

@endsection

@push('scripts')
<script>
    function buscarCep(cep) {
        cep = cep.replace(/\D/g, '');
        if (cep != "") {
            var validacep = /^[0-9]{8}$/;
            if(validacep.test(cep)) {
                document.getElementById('endereco').value = "...";
                document.getElementById('bairro').value = "...";
                document.getElementById('cidade').value = "...";
                document.getElementById('estado').value = "...";

                var script = document.createElement('script');
                script.src = 'https://viacep.com.br/ws/'+ cep + '/json/?callback=meu_callback';
                document.body.appendChild(script);
            } else {
                alert("Formato de CEP inválido.");
            }
        }
    }

    function meu_callback(conteudo) {
        if (!("erro" in conteudo)) {
            document.getElementById('endereco').value = (conteudo.logradouro);
            document.getElementById('bairro').value = (conteudo.bairro);
            document.getElementById('cidade').value = (conteudo.localidade);
            document.getElementById('estado').value = (conteudo.uf);
        } else {
            alert("CEP não encontrado.");
            // Não limpa os campos na edição para não perder o que já estava salvo caso o CEP dê erro
        }
    }
</script>
@endpush