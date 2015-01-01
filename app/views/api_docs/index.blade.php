@extends('api_docs.default_api_docs')

@section('docs')
  @if (isset($api_docs))
    @foreach ($api_docs as $doc)
            <!-- {{$doc->context}} -->
            <div id="{{$doc->id}}">
              <h2><span class="doc_title_static">{{{$doc->context}}}</span>
                @if ($admin)
                  <div class="float_right">
                    <button type="button" class="btn btn-primary btn_doc_edit">编辑</button>
                    <button type="button" class="btn btn-success btn_doc_commit">提交</button>
                  </div>
                @endif
              </h2>
              <input class="{{'doc_title'}}" style="display: none; width: 100%" value="{{{$doc->context}}}"/>
              <ul id="{{'doc_'.$doc->id}}">
      @foreach ($doc->items as $item)
        @if ($item['type'] == 'default')
                <li id="{{'li_'.$item['id'].'_'.$doc->id}}">{{$item['title'].'：'}}
                  <span id="{{'li_span_'.$item['id'].'_'.$doc->id}}" class="li_edit_pre">{{$item['context']}}</span>
                </li>
                <input id="{{'li_edit_'.$item['id'].'_'.$doc->id}}" class="li_edit" style="display: none; width: 100%" value="{{$item['context']}}" />
        @elseif ($item['type'] == 'detail')
                <li id="{{'li_'.$item['id'].'_'.$doc->id}}">{{$item['title'].'：'}}</li>
                <div class="highlight li_edit_pre" >
                  <pre><code id="{{'li_code_'.$item['id'].'_'.$doc->id}}">{{{$item['context']}}}</code></pre>
                </div>
                <textarea id="{{'li_edit_'.$item['id'].'_'.$doc->id}}" class="li_edit" style="display: none; width: 100%">{{{$item['context']}}}</textarea>
        @endif
      @endforeach
              </ul>
            </div>
    @endforeach
  @endif
@stop

@section("docs-nav")
  @if (isset($api_docs))
    @foreach ($api_docs as $doc)
              <li><a href="#{{$doc->id}}">{{{$doc->context}}}</a></li>
    @endforeach
  @endif
@stop