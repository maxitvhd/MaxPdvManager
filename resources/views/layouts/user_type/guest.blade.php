@extends('layouts.app')

@section('guest')
    @php
        $isAuthPage = Request::is('login') || Request::is('register') || Request::is('session');
    @endphp

    @if(\Request::is('login/forgot-password'))
        @include('layouts.navbars.guest.nav')
        @yield('content')
    @else
        @if(!$isAuthPage)
            <div class="container position-sticky z-index-sticky top-0">
                <div class="row">
                    <div class="col-12">
                        @include('layouts.navbars.guest.nav')
                    </div>
                </div>
            </div>
        @endif

        @yield('content')

        @if(!$isAuthPage)
            @include('layouts.footers.guest.footer')
        @endif
    @endif
@endsection