```bash
curl -X {{$route['methods'][0]}} \
    {{$route['methods'][0] == 'GET' ? '-G ' : ''}}"{{ rtrim($baseUrl, '/')}}/{{ ltrim($route['boundUri'], '/') }}@if(count($route['cleanQueryParameters']))?{!! \Mpociot\ApiDoc\Tools\Utils::printQueryParamsAsString($route['cleanQueryParameters']) !!}@endif" @if(count($route['headers']))\
<?php $numItems = count($route['headers']); $i = 0;?>
@foreach($route['headers'] as $header => $value)
    <?php $count = $i++; ?>
    -H "{{$header}}: {{ addslashes($value) }}"@if(! ($count === $numItems) || ($header == $count && count($route['bodyParameters']))) \
    <?php $i++; ?>
@endif
@endforeach
@endif
@if(count($route['cleanBodyParameters']))
    -d '{!! json_encode($route['cleanBodyParameters']) !!}'
@endif

```
