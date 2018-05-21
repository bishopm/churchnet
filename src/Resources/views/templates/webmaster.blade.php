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
    @yield('css')

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="{{url('/')}}"><i class="fa fa-home"></i><b>Church</b>Net</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav">  
        <li class="nav-item">
          <a class="nav-link" href="{{url('/')}}/admin/resources/create" title="Add new content"><i class="fa fa-plus"></i> Add new content</a>
        </li>
        <form class="form-inline">
          <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-secondary my-2 my-sm-0" type="submit"><i class="fa fa-search"></i> Search</button>
        </form>
      </ul>
      <ul class="navbar-nav ml-auto">
        @if(Auth::check())
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Admin</a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="{{url('/')}}/admin/circuits">Circuits</a>
              <a class="dropdown-item" href="{{url('/')}}/admin/settings">Settings</a>
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
    @yield('content')
  </section>
</body>
<section class="section">
  <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
  <script src="{{asset('/vendor/bishopm/js/bootstrap.min.js')}}"></script>
  @yield('js')
</section>
</html>