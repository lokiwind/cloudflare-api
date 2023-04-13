<?php
$apiKey = 'e884ff12aa36dbb1fa95bbbd4d28bf6c46c0';
$email = 'ipverifiedap@gmail.com';
$action = 'managed_challenge';
$notes = 'Belirli ülkeden gelen istekler için Managed Challenge uygula';
$url = "https://api.cloudflare.com/client/v4/user/firewall/access_rules/rules";
$headers = array(
  'X-Auth-Email: '.$email,
  'X-Auth-Key: '.$apiKey,
  'Content-Type: application/json'
);
$file = 'ip.txt';
$contents = file_get_contents($file);
$lines = explode("\n", $contents);
$ip_counts = array();
foreach ($lines as $line) {
  $line = trim($line);
  if (!empty($line)) {
    $fields = explode("|", $line);
    $ip = trim($fields[0]);
    $country_code = trim(end($fields));
    if (isset($ip_counts[$country_code])) {
      $ip_counts[$country_code]['ips'][] = $ip;
      $ip_counts[$country_code]['count']++;
    } else {
      $ip_counts[$country_code]['ips'][] = $ip;
      $ip_counts[$country_code]['count'] = 1;
    }
  }
}
$countries_to_block = array();
foreach ($ip_counts as $country_code => $count) {
  if ($count['count'] >= 10) {
    $countries_to_block[] = $country_code;
  }
}
foreach ($countries_to_block as $country) {
  $data = array(
    'mode' => $action,
    'notes' => $notes,
    'configuration' => array(
      'target' => 'country',
      'value' => $country
    ),
    'allowed_modes' => array(
      'block',
      'challenge',
      'js_challenge',
      'whitelist',
      'managed_challenge'
    )
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  if ($httpCode == 200) {
  $ipsToDelete = $ip_counts[$country]['ips'];
  foreach ($ipsToDelete as $ipToDelete) {
    $contents = str_replace($ipToDelete . '|' . $country . "\n", '', $contents);
  }
  file_put_contents($file, $contents);
} elseif ($httpCode == 400) {
  $ipsToDelete = $ip_counts[$country]['ips'];
  foreach ($ipsToDelete as $ipToDelete) {
    $contents = str_replace($ipToDelete . '|' . $country . "\n", '', $contents);
  }
  file_put_contents($file, $contents);
}
  echo $response;
}
?>
