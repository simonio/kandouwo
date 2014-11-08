<?php
?>

<!DOCTYPE HTML>
<!--
/*
 * jQuery File Upload Plugin Demo 9.1.0
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
-->
<html lang="en">
<head>
<!-- Force latest IE rendering engine or ChromeFrame if installed -->
<!--[if IE]>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<![endif]-->
<meta charset="utf-8">

<title>
  @section('title')
    看豆窝
  @show
</title>

<meta name="description" content="Kandouwo App Files">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap styles -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<!-- Generic page styles -->
<link rel="stylesheet" href="public/css/style.css">
<link rel="stylesheet" href="public/css/font-awesome.min.css">
<!-- blueimp Gallery styles -->
<link rel="stylesheet" href="//blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="public/css/jquery.fileupload.css">
<link rel="stylesheet" href="public/css/jquery.fileupload-ui.css">
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="public/css/jquery.fileupload-noscript.css"></noscript>
<noscript><link rel="stylesheet" href="public/css/jquery.fileupload-ui-noscript.css"></noscript>
@yield('styles')
</head>
<body>

@include('layouts.partials.nav')

<div class="container">
  <h2>抢鲜APP</h2>
  
	<ul class="nav nav-tabs">
    <li class="{{ (Request::is('apps') || Request::is('apps.android') ? 'active' : '') }}"><a href="{{ URL::route('android') }}">Android</a></li>
    <li class="{{ (Request::is('apps.ios') ? 'active' : '') }}"><a href="{{ URL::route('ios') }}">IOS</a></li>
  </ul>
  
  <br>
  @yield('content')
</div>

<!-- The blueimp Gallery widget -->
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>

<!-- footer -->
<div id="footer" class="footer container">
  <!--
  <blockquote>
    <p>File Upload widget with multiple file selection, drag&amp;drop support, progress bars, validation and preview images, audio and video for jQuery.<br>
    Supports cross-domain, chunked and resumable file uploads and client-side image resizing.<br>
    Works with any server-side platform (PHP, Python, Ruby on Rails, Java, Node.js, Go etc.) that supports standard HTML form file uploads.</p>
  </blockquote>
  
  
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Demo Notes</h3>
    </div>
    <div class="panel-body">
    </div>
  </div>
  -->
</div>

<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
  <tr class="template-upload fade">
    <td width="200">
      <span class="preview"></span>
    </td>
    <td>
      <p class="name">{%=file.name%}</p>
      <strong class="error text-danger"></strong>
    </td>
  <td><textarea style="width: 100%;" class="input-xlarge" id="textarea" rows="2"></textarea></td>
    <td>
      <p class="size">Processing...</p>
      <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
    </td>

    <td width="200">
      @if (Auth::check())
        {% if (!i && !o.options.autoUpload) { %}
          <button class="btn btn-primary start" disabled>
            <i class="glyphicon glyphicon-upload"></i>
            <span>上传</span>
          </button>
        {% } %}
        {% if (!i) { %}
          <button class="btn btn-warning cancel">
            <i class="glyphicon glyphicon-ban-circle"></i>
            <span>取消</span>
          </button>
        {% } %}
      @endif
    </td>
  </tr>
{% } %}
</script>

<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
  <tr class="template-download fade">
    <td>
      <span class="preview">
        {% if (file.thumbnailUrl) { %}
          <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
        {% } %}
      </span>
    </td>

    <td>
      <p class="name">
        {% if (file.url) { %}
          <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
        {% } else { %}
          <span>{%=file.name%}</span>
        {% } %}
      </p>
      {% if (file.error) { %}
        <div><span class="label label-danger">Error</span> {%=file.error%}</div>
      {% } %}
    </td>
  <td></td>
  <td>
    <span class="size">{%=o.formatFileSize(file.size)%}</span>
  </td>
  <td>
    @if (Auth::check())
      {% if (file.deleteUrl) { %}
        <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
          <i class="glyphicon glyphicon-trash"></i>
          <span>删除</span>
        </button>
        <input type="checkbox" name="delete" value="1" class="toggle">
      {% } else { %}
        <button class="btn btn-warning cancel">
          <i class="glyphicon glyphicon-ban-circle"></i>
          <span>取消</span>
        </button>
      {% } %}
    @endif
  </td>
  </tr>
{% } %}
</script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="public/js/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="//blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="//blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="//blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<!-- blueimp Gallery script -->
<script src="//blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="public/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="public/js/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="public/js/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="public/js/jquery.fileupload-image.js"></script>
<!-- The File Upload audio preview plugin -->
<script src="public/js/jquery.fileupload-audio.js"></script>
<!-- The File Upload video preview plugin -->
<script src="public/js/jquery.fileupload-video.js"></script>
<!-- The File Upload validation plugin -->
<script src="public/js/jquery.fileupload-validate.js"></script>
<!-- The File Upload user interface plugin -->
<script src="public/js/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
<script src="public/js/main.js"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="js/cors/jquery.xdr-transport.js"></script>
<![endif]-->
</body> 
</html>
