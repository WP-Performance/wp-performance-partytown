<?php

namespace WPPerformance\partytown\inc;


add_action('rest_api_init', function () {
  // if activate
  if (PR_PROXY === true) {
    register_rest_route('partytown/pr', 'getPR', array(
      'methods'  => 'GET',
      'callback' =>  __NAMESPACE__ . '\getPR',
      // control nonce
      'permission_callback' => function (\WP_REST_Request $request) {
        $nonce = $request->get_param('pt_nonce');
        // nonce don't work in API REST because user id is always 0
        return wp_verify_nonce($nonce, 'partytown_proxy') || $nonce === PR_PROXY_KEY;
      }
    ));
  }
});


function reformat($headers)
{
  foreach ($headers as $name => $value) {
    if ($value !== '') {
      yield "$name: $value";
    }
  }
}

function getPR(\WP_REST_Request $request)
{

  $method = $request->get_method();
  // url in param
  $urlRaw = sanitize_url($request->get_param('url'));

  // extract url
  $url_components = parse_url($urlRaw);
  parse_str($url_components['query'], $params);
  if (!array_key_exists('url', $params)) {
    return '';
  }
  $proxied_url = esc_url($params['url']);
  $proxied_host = parse_url($proxied_url)['host'];

  // init curl
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
  curl_setopt($ch, CURLOPT_URL, $proxied_url);

  // transfer header
  $h = $request->get_headers();
  foreach ($h as $key => $value) {
    if (
      $key !== 'host'
      && $key !== 'cookie'
    ) {
      $request_headers[$key] = sanitize_text_field($value[0]);
    }
  }
  // set to host the service
  $request_headers['Host'] = $proxied_host;
  // website host
  $request_headers['X-Forwarded-Host'] = $_SERVER['SERVER_NAME'];
  // header from request
  $request_headers = iterator_to_array(reformat($request_headers));
  curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
  // set cookies
  if (array_key_exists('cookie', $h)) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, $h['cookie']);
  }

  // message body
  $request_body = file_get_contents('php://input');
  curl_setopt($ch, CURLOPT_POSTFIELDS, sanitize_text_field($request_body));

  $response_headers = [];
  // catch header from response
  curl_setopt(
    $ch,
    CURLOPT_HEADERFUNCTION,
    function ($curl, $header) use (&$response_headers) {
      $len = strlen($header);
      $header = explode(':', $header, 2);
      // ignore invalid headers
      if (count($header) < 2)
        return $len;
      $response_headers[strtolower(trim($header[0]))][] = trim($header[1]);
      return $len;
    }
  );
  $response_body = curl_exec($ch);
  $response_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
  curl_close($ch);

  // return header from curl response
  http_response_code($response_code);
  foreach ($response_headers as $name => $values) {
    foreach ($values as $value) {
      header("$name: $value");
    }
  }
  echo $response_body;
  exit;
}
