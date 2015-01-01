$(function(){
  add_event();
}
);

function add_event()
{
  $(".btn_doc_add").bind({
    click : function() {
      var doc_template = '<div id="doc_new"> \
              <h2><span class="doc_new_title_static">标题</span> \
                  <div class="float_right"> \
                    <button type="button" class="btn btn-success btn_doc_new_commit">提交</button> \
                  </div> \
              </h2> \
              <input class="doc_new_title" style="width: 100%" value="标题"/> \
              <ul id="doc_new_detail"> \
                <li id="li_doc_new_http">HTTP：</li> \
                <input id="li_doc_new_http_input" class="li_edit" style="width: 100%" value="POST" /> \
                <li id="li_doc_new_token">认证：</li> \
                <input id="li_doc_new_token_input" class="li_edit" style="width: 100%" value="--" /> \
                <li id="li_doc_new_uri">URI：</li> \
                <input id="li_doc_new_uri_input" class="li_edit" style="width: 100%" value="" /> \
                <li id="li_doc_new_param">参数：</li> \
                <textarea id="li_doc_new_param_txt" class="li_edit" style="width: 100%"></textarea> \
                <li id="li_doc_new_return">返回：</li> \
                <textarea id="li_doc_new_return_txt" class="li_edit" style="width: 100%"></textarea> \
                <li id="li_doc_new_example">示例：</li> \
                <textarea id="li_doc_new_example_txt" class="li_edit" style="width: 100%"></textarea> \
                <li id="li_doc_new_error_code">错误码：</li> \
                <textarea id="li_doc_new_error_code_txt" class="li_edit" style="width: 100%"></textarea> \
              </ul> \
            </div>';
            
      $(".col-md-10").append(doc_template);
      $("html,body").animate({scrollTop:$("#doc_new").offset().top},500);
      $(".btn_doc_new_commit").bind({
        click : function(){
          var _title = $('.doc_new_title')[0].value;
          var _http = $('#li_doc_new_http_input')[0].value;
          var _token = $('#li_doc_new_token_input')[0].value;
          var _uri = $('#li_doc_new_uri_input')[0].value;
          var _param = $('#li_doc_new_param_txt').val();
          var _return = $('#li_doc_new_return_txt').val();
          var _example = $('#li_doc_new_example_txt').val();
          var _error_code = $('#li_doc_new_error_code_txt').val();
          console.debug(_return);
      
          $.ajax({
            url: '/api/add_doc',
            type: 'POST',
            async: false,
            data: {
              _title: _title,
              _http: _http,
              _token: _token,
              _uri: _uri,
              _param: _param,
              _return: _return,
              _example: _example,
              _error_code: _error_code
            },
            dataType: 'json',
            success: function(data){
              if(data.data.code == 0){
                window.location.reload();
              }
              else {
                alert(data.data.msg);
              }
            },
            error: function(data){
            }
    	    });
        }
      });
    }
  });
  
  $(".btn_doc_edit").bind({
    
    click : function(){
      var doc = $(this).closest('div').parent().parent();
      toggle_edit(doc);
    }
  });
  
  $(".btn_doc_commit").bind({

    click : function(){
      var _id = $(this).closest('div').parent().parent().attr('id');
      var _title = $(this).closest('div').parent().parent().find('.doc_title')[0].value;
      var _http = $('#li_edit_http_'+_id)[0].value;
      var _token = $('#li_edit_token_'+_id)[0].value;
      var _uri = $('#li_edit_uri_'+_id)[0].value;
      var _param = $('#li_edit_param_'+_id).val();
      var _return = $('#li_edit_return_'+_id).val();
      var _example = $('#li_edit_example_'+_id).val();
      var _error_code = $('#li_edit_error_code_'+_id).val();
      console.debug(_return);
      
      var _this = $(this);
            
      $.ajax({
        url: '/api/edit_doc',
        type: 'POST',
        async: false,
        data: {
          _id: _id,
          _title: _title,
          _http: _http,
          _token: _token,
          _uri: _uri,
          _param: _param,
          _return: _return,
          _example: _example,
          _error_code: _error_code
        },
        dataType: 'json',
        success: function(data){
          if(data.data.code == 0){
            var doc = _this.closest('div').parent().parent();
            toggle_edit(doc);
            update_edit(doc);
          }
          else {
            alert(data.data.msg);
          }
        },
        error: function(data){
        }
	    });
    }
    
  });
}

function toggle_edit(doc)
{
  doc.find('.doc_title').toggle();
  detail = doc.find('ul');
  detail.find('.zero-clipboard').toggle();
  detail.find('.li_edit_pre').toggle();
  detail.find('.li_edit').toggle();
}

function update_edit(doc)
{
  doc.find('.doc_title_static').html(doc.find('.doc_title')[0].value);
  _id = doc.attr('id');
  $('#li_span_http_'+_id).html($('#li_edit_http_'+_id)[0].value);
  $('#li_span_token_'+_id).html($('#li_edit_token_'+_id)[0].value);
  $('#li_span_uri_'+_id).html($('#li_edit_uri_'+_id)[0].value);
  $('#li_code_param_'+_id).html($('#li_edit_param_'+_id).val());
  $('#li_code_return_'+_id).html($('#li_edit_return_'+_id).val());
  $('#li_code_example_'+_id).html($('#li_edit_example_'+_id).val());
  $('#li_code_error_code_'+_id).html($('#li_edit_error_code_'+_id).val());
}