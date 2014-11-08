@extends('upload.index')

@section('title')
{{ lang('Apps') }}
@stop

@section('content')
  <!-- The file upload form used as target for the file upload widget -->
  <form id="fileupload" action="" method="POST" enctype="multipart/form-data"><!--//jquery-file-upload.appspot.com/-->
    <!-- Redirect browsers with JavaScript disabled to the origin page 
    <noscript><input type="hidden" name="redirect" value="https://blueimp.github.io/jQuery-File-Upload/"></noscript>-->
    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
    @if (Auth::check())
      <div class="row fileupload-buttonbar">
        <div class="col-lg-7">
          <!-- The fileinput-button span is used to style the file input field as button -->
          <span class="btn btn-success fileinput-button">
            <i class="glyphicon glyphicon-plus"></i>
            <span>添加文件</span>
            <input type="file" name="files[]" multiple>
          </span>
          <button type="submit" class="btn btn-primary start">
            <i class="glyphicon glyphicon-upload"></i>
            <span>上传</span>
          </button>
          <button type="reset" class="btn btn-warning cancel">
            <i class="glyphicon glyphicon-ban-circle"></i>
            <span>取消上传</span>
          </button>
          <button type="button" class="btn btn-danger delete">
            <i class="glyphicon glyphicon-trash"></i>
            <span>删除</span>
          </button>
          <input type="checkbox" class="toggle">
          <!-- The global file processing state -->
          <span class="fileupload-process"></span>
        </div>
        <!-- The global progress state -->
        <div class="col-lg-5 fileupload-progress fade">
          <!-- The global progress bar -->
          <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar progress-bar-success" style="width:0%;"></div>
          </div>
          <!-- The extended global progress state -->
          <div class="progress-extended">&nbsp;</div>
        </div>
      </div>
    @endif
    
    <!-- The table listing the files available for upload/download -->
    <table role="presentation" class="table table-striped" id="filelist">
      <tbody class="files">
        <thead>
          <tr>
            <th width="100">日期</th>
            <th width="240">名称</th>
            <th>描述</th>
            <th width="200">大小</th>
            <th width="160" style="text-align:right"> </th>
          </tr>
        </thead>
      </tbody>
    </table>
  </form>
@stop