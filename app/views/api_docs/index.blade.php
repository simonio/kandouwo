@extends('api_docs.default_api_docs')

@section('docs')
  @if (isset($api_docs))
    @foreach ($api_docs as $doc)
            <!-- {{$doc->context}} -->
            <h2 id="{{$doc->id}}">{{{$doc->context}}}</h2>
            <ul>
      @foreach ($doc->items as $item)
        @if ($item['type'] == 'default')
              <li>{{$item['context']}}</li>
        @elseif ($item['type'] == 'detail')
              <div class="highlight" >
                <pre><code>{{{$item['context']}}}</code></pre>
              </div>
        @endif
      @endforeach
            </ul>
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