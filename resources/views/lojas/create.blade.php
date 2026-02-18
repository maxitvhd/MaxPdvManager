@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">

      @if($errors->any())
        <div class="alert alert-danger text-white" role="alert">
            <h6 class="text-white">Corrija os erros abaixo:</h6>
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
              <div class="d-flex justify-content-between">
                  <h5><i class="fas fa-store me-2"></i>Criar Nova Loja</h5>
                  <a href="{{ route('lojas.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                  </a>
              </div>
            </div>
            
            <div class="card-body">
              <form action="{{ route('lojas.store') }}" method="POST">
                @csrf
                
                <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Informações Gerais</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome" class="form-control-label">Nome da Loja <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="nome" id="nome" required placeholder="Ex: Minha Loja Matriz">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="form-control-label">E-mail Comercial <span class="text-danger">*</span></label>
                            <input class="form-control" type="email" name="email" id="email" required placeholder="contato@loja.com">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="telefone" class="form-control-label">Telefone / WhatsApp <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="telefone" id="telefone" required placeholder="(00) 00000-0000">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status" class="form-control-label">Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" id="status" required>
                                <option value="1" selected>Ativa</option>
                                <option value="0">Inativa</option>
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
                            <input class="form-control" type="text" name="cnpj" id="cnpj" required placeholder="00.000.000/0001-00">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cpf" class="form-control-label">CPF do Responsável <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="cpf" id="cpf" required placeholder="000.000.000-00">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="descricao" class="form-control-label">Descrição da Loja</label>
                            <textarea class="form-control" name="descricao" id="descricao" rows="3" required placeholder="Descreva brevemente a atividade da loja..."></textarea>
                        </div>
                    </div>
                </div>

                <hr class="horizontal dark my-3">

                <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Endereço</h6>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cep" class="form-control-label">CEP <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="cep" id="cep" required placeholder="00000-000" onblur="buscarCep(this.value)">
                            <small class="text-xs text-muted">Digite para buscar</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="endereco" class="form-control-label">Logradouro <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="endereco" id="endereco" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="bairro" class="form-control-label">Bairro <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="bairro" id="bairro" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="cidade" class="form-control-label">Cidade <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="cidade" id="cidade" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="estado" class="form-control-label">Estado (UF) <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="estado" id="estado" maxlength="2" required placeholder="SP">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('lojas.index') }}" class="btn btn-light m-0">Cancelar</a>
                    <button type="submit" class="btn bg-gradient-success m-0 ms-2">
                        <i class="fas fa-save me-2"></i> Salvar Loja
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
    // Função para buscar CEP automaticamente na API ViaCEP
    function buscarCep(cep) {
        // Remove caracteres não numéricos
        cep = cep.replace(/\D/g, '');

        if (cep != "") {
            // Expressão regular para validar o CEP
            var validacep = /^[0-9]{8}$/;

            if(validacep.test(cep)) {
                // Preenche os campos com "..." enquanto consulta webservice
                document.getElementById('endereco').value = "...";
                document.getElementById('bairro').value = "...";
                document.getElementById('cidade').value = "...";
                document.getElementById('estado').value = "...";

                // Cria um elemento javascript
                var script = document.createElement('script');

                // Sincroniza com o callback
                script.src = 'https://viacep.com.br/ws/'+ cep + '/json/?callback=meu_callback';

                // Insere script no documento e carrega o conteúdo
                document.body.appendChild(script);
            } else {
                alert("Formato de CEP inválido.");
            }
        }
    }

    // Callback da função de busca
    function meu_callback(conteudo) {
        if (!("erro" in conteudo)) {
            // Atualiza os campos com os valores
            document.getElementById('endereco').value = (conteudo.logradouro);
            document.getElementById('bairro').value = (conteudo.bairro);
            document.getElementById('cidade').value = (conteudo.localidade);
            document.getElementById('estado').value = (conteudo.uf);
        } else {
            // CEP não Encontrado
            alert("CEP não encontrado.");
            document.getElementById('endereco').value = "";
            document.getElementById('bairro').value = "";
            document.getElementById('cidade').value = "";
            document.getElementById('estado').value = "";
        }
    }
</script>
@endpush