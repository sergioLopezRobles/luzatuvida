<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
{{--    <meta name="viewport" content="width=device-width, initial-scale=1">--}}

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titulo','Luz a tu vida')</title>

    @include('parciales.link')
    @include('parciales.script')
    @include('parciales.spinner')
    @include('parciales.notificaciones')
</head>
<body >
    <div id="app">
        <nav id="navbar" class="navbar navbar-expand-md shadow-sm navbar-custom" style="width:100%;">
            @if(Auth::check())
                <i id="botonsidebar" class="fas fa-bars" onclick="openNav()"></i>
            @endif
          <div class="container">
               <a  href="{{ url('/') }}"><img id="logo" src="/imagenes/general/administracion/logo.png" alt=""></a>
              <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                      aria-label="{{ __('Toggle navigation') }}">
                  <span class="navbar-toggler-icon"></span>
              </button>

              <div class="collapse navbar-collapse" id="navbarSupportedContent">
                  <!-- Left Side Of Navbar -->
                  <ul class="navbar-nav mr-auto">

                  </ul>

                  <!-- Right Side Of Navbar -->
                  <ul class="navbar-nav ml-auto" style="margin-left:10%;">
                      <!-- Authentication Links -->
                      @guest
                      @else
                          <li class="dropdown">
                              <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" v-pre>
                                  {{ Auth::user()->name }}
                              </a>

                              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                        @lang('mensajes.cerrarsesion')
                                    </a>
                                  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                      @csrf
                                  </form>
                              </div>
                          </li>
                      @endguest
                  </ul>
              </div>
          </div>
        </nav>
        <main class="py-4 principal" >
            @if(Auth::check())
            <div id="mySidenav" class="bg-dark sidenav">
                @include('administracion.sidebar') {{-- TO DO EL CONTENIDO REFERENTE SOLO AL SIDEBAR--}}
            </div>
            @endif
            <div id="main">
                @yield('content')
            </div>
        </main>
        <footer class="page-footer font-small blue">
          <div class="footer-copyright text-center py-3">
            © Todos los derechos reservados | Luz a tu vida
          </div>
        </footer>
    </div>
</body>
</html>
