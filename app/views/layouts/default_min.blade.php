<!DOCTYPE HTML>

<html lang="zh-cn">

  <head>
    <meta charset="utf-8">
    <meta name="description" content="Kandouwo App Files">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Force latest IE rendering engine or ChromeFrame if installed -->
    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <![endif]-->
    <title>
      @section('title')
        看豆窝
      @show
    </title>
    <link href="http://cdn.bootcss.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
    @yield('styles')
  </head>

  <body>
    
    @yield('content_before')
    <div class="container">
    @yield('content')
    </div>
    @yield('content_after')
    

    <script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.js"></script>
    <!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
    <script src="http://cdn.bootcss.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <!-- IE emulate mode warning -->
    <script src="../public/js/ie-emulation-modes-warning.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../public/js/ie10-viewport-bug-workaround.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    @yield('script')

  </body> 
</html>
