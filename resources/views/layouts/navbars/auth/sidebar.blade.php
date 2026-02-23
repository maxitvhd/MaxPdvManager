<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-white"
  id="sidenav-main" style="height: calc(100vh - 2rem) !important;">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
      aria-hidden="true" id="iconSidenav"></i>
    <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('dashboard') }}">
      <img src="../assets/img/logo-ct.png" class="navbar-brand-img h-100" alt="...">
      <span class="ms-3 font-weight-bold">MaxCheckout</span>
    </a>
  </div>
  <hr class="horizontal dark mt-0">
  <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main"
    style="height: calc(100vh - 8rem) !important;">
    <ul class="navbar-nav">

      <!-- ================= Dashboard Section ================= -->
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#dashboardsExamples"
          class="nav-link {{ Request::is('dashboard*') ? 'active' : '' }}" aria-controls="dashboardsExamples"
          role="button" aria-expanded="{{ Request::is('dashboard*') ? 'true' : 'false' }}">
          <div
            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center me-2">
            <i class="fas fa-chart-pie {{ Request::is('dashboard*') ? 'text-white' : 'text-dark' }} text-lg"></i>
          </div>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
        <div class="collapse {{ Request::is('dashboard*') ? 'show' : '' }}" id="dashboardsExamples">
          <ul class="nav ms-4">
            <li class="nav-item">
              <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"> <i class="fas fa-home"></i> </span>
                <span class="sidenav-normal"> Geral </span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Request::is('dashboard/operacional') ? 'active' : '' }}"
                href="{{ route('dashboard.operacional') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"> <i class="fas fa-chart-line"></i>
                </span>
                <span class="sidenav-normal"> Operacional </span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Request::is('dashboard/financeiro') ? 'active' : '' }}"
                href="{{ route('dashboard.financeiro') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"> <i class="fas fa-dollar-sign"></i>
                </span>
                <span class="sidenav-normal"> Financeiro </span>
              </a>
            </li>
          </ul>
        </div>
      </li>

      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6 sidenav-heading">Mercado</h6>
      </li>

      <!-- ================= Lojas & Gerenciamento ================= -->
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#mercadoExamples"
          class="nav-link {{ Request::is('lojas*') || Request::is('produtos*') || Request::is('funcionarios*') || Request::is('checkouts*') || Request::is('licencas*') || Request::is('pagamentos/faturas*') ? 'active' : '' }}"
          aria-controls="mercadoExamples" role="button"
          aria-expanded="{{ Request::is('lojas*') || Request::is('produtos*') || Request::is('funcionarios*') || Request::is('checkouts*') || Request::is('licencas*') || Request::is('pagamentos/faturas*') ? 'true' : 'false' }}">
          <div
            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center me-2">
            <i
              class="fas fa-store {{ Request::is('lojas*') || Request::is('produtos*') || Request::is('funcionarios*') || Request::is('checkouts*') || Request::is('licencas*') || Request::is('pagamentos/faturas*') ? 'text-white' : 'text-dark' }} text-lg"></i>
          </div>
          <span class="nav-link-text ms-1">Gerenciamento</span>
        </a>
        <div
          class="collapse {{ Request::is('lojas*') || Request::is('produtos*') || Request::is('funcionarios*') || Request::is('checkouts*') || Request::is('licencas*') || Request::is('pagamentos/faturas*') ? 'show' : '' }}"
          id="mercadoExamples">
          <ul class="nav ms-4">
            <li class="nav-item">
              <a class="nav-link {{ Request::is('lojas*') ? 'active' : '' }}" href="{{ route('lojas.index') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"> <i class="fas fa-store-alt"></i>
                </span>
                <span class="sidenav-normal"> Lojas </span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Request::is('funcionarios*') ? 'active' : '' }}"
                href="{{ route('funcionarios.index') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"> <i class="fas fa-user-tag"></i> </span>
                <span class="sidenav-normal"> Funcionários </span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Request::is('produtos*') ? 'active' : '' }}" href="{{ route('produtos.index') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"> <i class="fas fa-box-open"></i> </span>
                <span class="sidenav-normal"> Produtos </span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Request::is('checkouts*') ? 'active' : '' }}" href="{{ route('checkouts.index') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"> <i class="fas fa-cash-register"></i>
                </span>
                <span class="sidenav-normal"> Checkouts </span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Request::is('licencas*') ? 'active' : '' }}" href="{{ route('licencas.index') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"> <i class="fas fa-id-badge"></i> </span>
                <span class="sidenav-normal"> Licenças </span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Request::is('pagamentos/faturas*') ? 'active' : '' }}"
                href="{{ route('pagamentos.faturas') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"> <i
                    class="fas fa-file-invoice-dollar"></i>
                </span>
                <span class="sidenav-normal"> Faturas </span>
              </a>
            </li>
          </ul>
        </div>
      </li>


      <!-- ================= MaxPublica (todos lojistas) ================= -->
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6 sidenav-heading">MaxPublica</h6>
      </li>
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#maxpublicaExamples"
          class="nav-link {{ Request::is('lojista/maxdivulga*') ? 'active' : '' }}" aria-controls="maxpublicaExamples"
          role="button" aria-expanded="{{ Request::is('lojista/maxdivulga*') ? 'true' : 'false' }}">
          <div
            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center me-2">
            <i
              class="fas fa-bullhorn {{ Request::is('lojista/maxdivulga*') ? 'text-white' : 'text-dark' }} text-lg"></i>
          </div>
          <span class="nav-link-text ms-1">MaxPublica</span>
        </a>
        <div class="collapse {{ Request::is('lojista/maxdivulga*') ? 'show' : '' }}" id="maxpublicaExamples">
          <ul class="nav ms-4">
            <li class="nav-item">
              <a class="nav-link {{ Request::is('lojista/maxdivulga') ? 'active' : '' }}"
                href="{{ route('lojista.maxdivulga.index') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"><i
                    class="fas fa-photo-video"></i></span>
                <span class="sidenav-normal">Campanhas</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Request::is('lojista/maxdivulga/create*') ? 'active' : '' }}"
                href="{{ route('lojista.maxdivulga.create') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"><i
                    class="fas fa-plus-circle"></i></span>
                <span class="sidenav-normal">Nova Campanha</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Request::is('lojista/maxdivulga/themes*') ? 'active' : '' }}"
                href="{{ route('lojista.maxdivulga.themes') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"><i class="fas fa-palette"></i></span>
                <span class="sidenav-normal">Theme Studio</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Request::is('lojista/maxdivulga/canais*') ? 'active' : '' }}"
                href="{{ route('lojista.maxdivulga.canais.index') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"><i class="fas fa-share-alt"></i></span>
                <span class="sidenav-normal">Canais Sociais</span>
              </a>
            </li>
          </ul>
        </div>
      </li>

      <!-- ================= Admin Section ================= -->
      @hasanyrole('admin|super-admin')
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6 sidenav-heading">Administração</h6>
      </li>

      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#adminExamples"
          class="nav-link {{ Request::is('admin*') || Request::is('pagamentos/configuracoes*') || Request::is('usuarios*') ? 'active' : '' }}"
          aria-controls="adminExamples" role="button"
          aria-expanded="{{ Request::is('admin*') || Request::is('pagamentos/configuracoes*') || Request::is('usuarios*') ? 'true' : 'false' }}">
          <div
            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center me-2">
            <i
              class="fas fa-user-shield {{ Request::is('admin*') || Request::is('pagamentos/configuracoes*') || Request::is('usuarios*') ? 'text-white' : 'text-dark' }} text-lg"></i>
          </div>
          <span class="nav-link-text ms-1">Sistema</span>
        </a>
        <div
          class="collapse {{ Request::is('admin*') || Request::is('pagamentos/configuracoes*') || Request::is('usuarios*') ? 'show' : '' }}"
          id="adminExamples">
          <ul class="nav ms-4">
            <li class="nav-item">
              <a class="nav-link {{ Request::is('admin/planos*') ? 'active' : '' }}" href="{{ route('planos.index') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"> <i class="fas fa-box"></i> </span>
                <span class="sidenav-normal"> Planos </span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Request::is('admin/adicionais*') ? 'active' : '' }}"
                href="{{ route('adicionais.index') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"> <i class="fas fa-plus-circle"></i>
                </span>
                <span class="sidenav-normal"> Adicionais </span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Request::is('pagamentos/configuracoes*') ? 'active' : '' }}"
                href="{{ route('pagamentos.configuracoes') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"> <i class="fas fa-cogs"></i> </span>
                <span class="sidenav-normal"> Config. Pagamento </span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Request::is('usuarios*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"> <i class="fas fa-users"></i> </span>
                <span class="sidenav-normal"> Usuários </span>
              </a>
            </li>
            {{-- MaxPublica Admin --}}
            <li class="nav-item mt-1">
              <a class="nav-link {{ Request::is('admin/MaxDivulga') ? 'active' : '' }}"
                href="{{ route('admin.maxdivulga.index') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"><i class="fas fa-robot"></i></span>
                <span class="sidenav-normal">MaxPublica IA</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Request::is('admin/MaxDivulga/themes*') ? 'active' : '' }}"
                href="{{ route('admin.maxdivulga.themes') }}">
                <span class="sidenav-mini-icon text-xs text-center w-auto me-2"><i
                    class="fas fa-paint-roller"></i></span>
                <span class="sidenav-normal">Temas Globais</span>
              </a>
            </li>
          </ul>
        </div>
      </li>
      @endhasanyrole

    </ul>
  </div>
</aside>