<?php
// $queryString = http_build_query([
  // 'access_key' => 'c06d8e2f37e9ad28c7baa29ffaacdfe6',
  // 'query' => 'sample',
  // 'gl' => 'jp'
// ]);

// $ch = curl_init(sprintf('%s?%s', 'http://api.serpstack.com/search', $queryString));
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// $json = curl_exec($ch);
// curl_close($ch);

// $api_result = json_decode($json, true);
// var_dump($api_result);

// echo "Total results: ", $api_result['search_information']['total_results'], PHP_EOL;

// foreach ($api_result['organic_results'] as $number => $result) {
  // echo "{$number}. {$result['title']}", PHP_EOL;
// }



include('simple_html_dom.php');
 
function strip_tags_content($text, $tags = '', $invert = FALSE) {
	/*
	This function removes all html tags and the contents within them
	unlike strip_tags which only removes the tags themselves.
	*/
	//removes <br> often found in google result text, which is not handled below
	$text = str_ireplace('<br>', '', $text);
 
	preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
	$tags = array_unique($tags[1]);
 
	if(is_array($tags) AND count($tags) > 0) {
		//if invert is false, it will remove all tags except those passed a
		if($invert == FALSE) {
			return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
		//if invert is true, it will remove only the tags passed to this function
		} else {
			return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text);
		}
	//if no tags were passed to this function, simply remove all the tags
	} elseif($invert == FALSE) {
		return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
	}
 
	return $text;
}
 
function file_get_contents_curl($url) {
	/*
	This is a file_get_contents replacement function using cURL
	One slight difference is that it uses your browser's idenity
	as it's own when contacting google. 
	*/
	$ch = curl_init();
 
	curl_setopt($ch, CURLOPT_USERAGENT,	$_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
 
	$data = curl_exec($ch);
	curl_close($ch);
 
	return $data;
}
 
//Set query if any passed
$q = isset($_GET['q'])?urlencode(str_replace(' ', '+', $_GET['q'])):'none';
 
//Obtain the first page html with the formated url
$data = file_get_contents_curl('https://www.google.co.jp/search?start=0&gl=jp&q='.$q);
 
/*
create a simple_html_dom object from the retreived string
you could also perform file_get_html("http://...") instead of
file_get_contents_curl above, but it wouldn't change the default
User-Agent
*/
 
$html = str_get_html($data); 
echo $html;
 
$result = array();
 
foreach($html->find('div.srg div.g') as $g)
{
	/*
	each search results are in a list item with a class name 'g'
	we are seperating each of the elements within, into an array
 
	Titles are stored within <h3><a...>{title}</a></h3>
	Links are in the href of the anchor contained in the <h3>...</h3>
	Summaries are stored in a div with a classname of 's'
	*/
 
	$s = $g->find('span.st', 0);
	$a = $g->find('a', 0);
	$t = $a->find('h3',0);
	$result[] = array('title' => strip_tags($t->innertext), 
		'link' => $a->href, 
		'description' => strip_tags_content($s->innertext));
}
 
// echo serialize($result);

echo "<textarea style='width: 1024px; height: 600px;'>";
print_r($result);
echo "</textarea>";

//Cleans up the memory 
$html->clear(); exit();
?>
