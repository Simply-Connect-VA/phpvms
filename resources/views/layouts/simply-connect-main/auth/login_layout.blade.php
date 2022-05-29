<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8"/>
  <link rel="apple-touch-icon" sizes="76x76" href="/assets/frontend/img/apple-icon.png">
  <link rel="icon" type="image/png" href="/assets/frontend/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
  <title>@yield('title') - {{ config('app.name') }}</title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
        name='viewport'/>
  <link href="//fonts.googleapis.com/css?family=Kanit:200,300,400,700,200i,300i,400i,700i" rel="stylesheet"/>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css"/>
  <link href="{{ public_asset('/assets/frontend/css/bootstrap.min.css') }}" rel="stylesheet"/>
  <link href="{{ public_mix('/assets/frontend/css/now-ui-kit.css') }}" rel="stylesheet"/>
  <link href="{{ public_asset('/assets/frontend/css/styles.css') }}" rel="stylesheet"/>
  <link href="{{public_asset('/assets/frontend/css/auth_login.css')}}" rel="stylesheet"/>
  @yield('css')
</head>

<body class="login-page" style="background: #067ec1;">
<!-- Navbar -->

<!-- End Navbar -->
<div class="page-header">
  @yield('content')
</div>
</body>

<script src="{{ public_asset('/assets/global/js/jquery.js') }}" type="text/javascript"></script>

@yield('scripts')

</html>
