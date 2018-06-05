<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ asset('/vendor/bishopm/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('/vendor/bishopm/css/admin.css')}}">
    @yield('css')
    <style>
      .dropdown-toggle::after {
        display: none;
      }
    </style>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="fixed-nav sticky-footer" id="page-top">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="{{url('/')}}"><b>Church</b>Net</a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Add new content">
          <a class="nav-link" href="{{url('/')}}/admin/resources/create">
            <i class="fa fa-fw fa-plus"></i>
            <span class="nav-link-text">Add new content</span>
          </a>
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Free church management software">
          <a class="nav-link" target="_blank" href="http://connexion.church.net.za">
            <i class="fa fa-fw fa-home"></i>
            <span class="nav-link-text">Connexion software</span>
          </a>
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Methodist churches in SA">
          <a class="nav-link" href="methodist/districts">
            <i class="fa fa-fw fa-church"></i>
            <span class="nav-link-text">Methodist churches</span>
          </a>
        </li>
        @auth
          <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Add new page">
            <a class="nav-link" href="{{url('/')}}/admin/pages/create">
              <i class="fa fa-fw fa-file-o"></i>
              <span class="nav-link-text">Add new page</span>
            </a>
          </li>
        @endauth
      </ul>
      <ul class="navbar-nav sidenav-toggler">
        <li class="nav-item">
          <a class="nav-link text-center" id="sidenavToggler">
            <i class="fa fa-fw fa-angle-left"></i>
          </a>
        </li>
      </ul>
      <ul class="navbar-nav ml-auto">
        <form class="form-inline my-2 my-lg-0 mr-lg-2">
          <div class="input-group">
            <input class="form-control" type="text" placeholder="Search for...">
            <span class="input-group-append">
              <button class="btn btn-primary" type="button">
                <i class="fa fa-search"></i>
              </button>
            </span>
          </div>
        </form>
        @if(Auth::check())
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Admin</a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="{{url('/')}}/admin/circuits">Circuits</a>
              <a class="dropdown-item" href="{{url('/')}}/admin/settings">Settings</a>
              <a class="dropdown-item" href="{{url('/')}}/admin/readings">Lectionary</a>
              <a class="dropdown-item" href="{{url('/')}}/admin/pages/create">Add new page</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
              <form id="logout-form" action="{{url('/')}}/logout" method="POST" style="display: none;"><input type="hidden" name="_token" value="{{csrf_token()}}"></form>
            </div>
          </li>
        @else
          <li class="nav-item">
            <a class="nav-link" href="{{url('/')}}/login" title="User login"><i class="fa fa-sign-in"></i> Login / Register</a>
          </li>
        @endif
      </ul>
    </div>
  </nav>
  <section class="section">
    <div class="content-wrapper">
      @yield('content')
    </div>
  </section>
</body>
<section class="section">
  <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="{{asset('/vendor/bishopm/js/bootstrap.min.js')}}"></script>
  <script src="{{asset('/vendor/bishopm/js/admin.js')}}"></script>
  @yield('js')
</section>
</html>