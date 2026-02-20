<!DOCTYPE html>

@if (\Request::is('rtl'))
  <html dir="rtl" lang="ar">
@else
  <html lang="pt_br">
@endif

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  @if (env('IS_DEMO'))
    <x-demo-metas></x-demo-metas>
  @endif

  <link rel="apple-touch-icon" sizes="76x76" href="/../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="/../assets/img/favicon.png">
  <title>
    MaxChechout Sistem
  </title>

  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />

  <link href="/../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="/../assets/css/nucleo-svg.css" rel="stylesheet" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <link id="pagestyle" href="/../assets/css/soft-ui-dashboard.css?v=1.0.3" rel="stylesheet" />
</head>

<body
  class="g-sidenav-show bg-gray-100 {{ (\Request::is('rtl') ? 'rtl' : (Request::is('virtual-reality') ? 'virtual-reality' : '')) }} ">

  @auth
    @yield('auth')
  @endauth
  @guest
    @yield('guest')
  @endguest

  <script src="/../assets/js/core/popper.min.js"></script>
  <script src="/../assets/js/core/bootstrap.min.js"></script>
  <script src="/../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="/../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="/../assets/js/plugins/chartjs.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @stack('rtl')
  @stack('dashboard')

  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>

  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="../assets/js/soft-ui-dashboard.js?v=1.0.3"></script>

  @stack('scripts')

  @if(session('success'))
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        Swal.fire('Sucesso!', '{{ session('success') }}', 'success');
      });
    </script>
  @endif

  @if(session('error'))
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        Swal.fire({
          icon: 'error',
          title: 'Atenção',
          text: '{{ session('error') }}',
          confirmButtonText: 'Ok'
        });
      });
    </script>
  @endif

</body>

</html>