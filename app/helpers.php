<?php

function cdn( $filepath )
{
    if (Config::get('app.url_static'))
    {
        return Config::get('app.url_static') . $filepath;
    }
    else
    {
        return Config::get('app.url') . $filepath;
    }
}

function getCdnDomain()
{
    return Config::get('app.url_static') ?: Config::get('app.url');
}

function lang($text)
{
    return str_replace('kandouwo.', '', trans('kandouwo.'.$text));
}

function debug_to_console( $data )
{
  if (Config::get('app.console'))
  {
    if ( is_array( $data ) )
        $output = "<script>console.log( '" . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( '" . $data . "' );</script>";

    echo $output;
  }
}