<?php
$queryString = http_build_query([
  'access_key' => 'c06d8e2f37e9ad28c7baa29ffaacdfe6',
  'query' => 'sample',
  'gl' => 'jp'
]);

$ch = curl_init(sprintf('%s?%s', 'http://api.serpstack.com/search', $queryString));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$json = curl_exec($ch);
curl_close($ch);

$api_result = json_decode($json, true);
var_dump($api_result);

echo "Total results: ", $api_result['search_information']['total_results'], PHP_EOL;

foreach ($api_result['organic_results'] as $number => $result) {
  echo "{$number}. {$result['title']}", PHP_EOL;
}
	
