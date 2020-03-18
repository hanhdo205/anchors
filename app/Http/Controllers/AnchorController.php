<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Pagination\LengthAwarePaginator;

use App\Anchor;

use Illuminate\Support\Facades\DB;

class AnchorController extends Controller
{
    /**
     * Display a listing of the keywords.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $anchors = Anchor::paginate(10);
        return view('anchors.index',compact('anchors',$anchors));
    }
	
	/**
     * Show the form for registering a new keyword.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('anchors.create');
    }
	
	/**
     * Store a newly registered keyword in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|unique:anchors',
        ]);

        Anchor::create($request->all());

        return redirect('/anchors');

    }
	
	/**
     * Removes all html tags and the contents within them unlike strip_tags which only removes the tags themselves.
     *
     */
	public static function strip_tags_content($text, $tags = '', $invert = FALSE)
	{
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
	
	/**
     * file_get_contents replacement function using cURL One slight difference is that it uses your browser's idenity as it's own when contacting google
     *
     */
	public static function file_get_contents_curl($url)
	{
		$ch = curl_init();
	 
		curl_setopt($ch, CURLOPT_USERAGENT,	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
	 
		$data = curl_exec($ch);
		curl_close($ch);
	 
		return $data;
	}
	
	
	/**
     * Scrape Google search results.
     *
     * @param $keyword
     * @return array
     */
	public static function scrape($id)
	{
		 
		include(app_path() . '\includes\simple_html_dom.php');
		$keyword = DB::table('anchors')->where('id', $id)->value('keyword');
		
		//Obtain the first page html with the formated url
		$data = self::file_get_contents_curl('https://www.google.co.jp/search?q='.$keyword.'&start=0&gl=jp');
		 
		/*
		create a simple_html_dom object from the retreived string
		you could also perform file_get_html("http://...") instead of
		file_get_contents_curl above, but it wouldn't change the default
		User-Agent
		*/
		 
		$html = str_get_html($data); 
		 
		$results = array();
		 
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
			$results[] = array('title' => strip_tags($t->innertext), 
				'link' => $a->href, 
				'description' => self::strip_tags_content($s->innertext));
		}
		
		//Cleans up the memory 
		$html->clear();
		
		return $results;
		
	}
	
	/**
     * Scrape Google search results.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function result(Request $request)
	{
		$id = $request->q;
		$results = self::scrape($id);
		//Anchor::where('keyword', $keyword)->update(array('status' => 2));
		
		return view('anchors.result',compact(['results','id']));
	}
	
	/**
     * Scrape Google search results.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function detail(Request $request)
	{
		$keyword = $request->keyword;
		$rank = $request->rank;
		$results = self::scrape($keyword);
		$result = $results[$rank];
		//Anchor::where('keyword', $keyword)->update(array('status' => 3));
		
		//Get the page's HTML source using file_get_contents.
		$html = self::file_get_contents_curl($result['link']);

		//Instantiate the DOMDocument class.
		$htmlDom = new \DOMDocument;

		//Parse the HTML of the page using DOMDocument::loadHTML
		@$htmlDom->loadHTML($html);

		//Extract the links from the HTML.
		$links = $htmlDom->getElementsByTagName('a');

		//Array that will contain our extracted links.
		$anchors = array();

		//Loop through the DOMNodeList.
		//We can do this because the DOMNodeList object is traversable.
		foreach($links as $link){

			//Get the link text.
			$linkText = $link->nodeValue;
			$linkType = 'Text';
			if(preg_match('/<img/', $linkText)) {
				$linkType = 'Img';
			}
			
			//Get the link in the href attribute.
			$linkHref = $link->getAttribute('href');

			//If the text is empty, skip it and don't
			//add it to our $anchors array
			if(strlen(trim($linkText)) == 0){
				continue;
			}
			
			//If the link is empty, skip it and don't
			//add it to our $anchors array
			if(strlen(trim($linkHref)) == 0){
				continue;
			}

			//Skip if it is a hashtag / anchor link.
			if($linkHref[0] == '#'){
				continue;
			}

			//Add the link to our $anchors array.
			$anchors[] = array(
				'text' => $linkText,
				'url' => $linkHref,
				'type' => $linkType,
			);
			
			// Get current page form url e.x. &page=1
			$currentPage = LengthAwarePaginator::resolveCurrentPage();
	 
			// Create a new Laravel collection from the array data
			$itemCollection = collect($anchors);
	 
			// Define how many items we want to be visible in each page
			$perPage = 10;
	 
			// Slice the collection to get the items to display in current page
			$currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
	 
			// Create our paginator and pass it to the view
			$paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
	 
			// set url path for generted links
			$paginatedItems->setPath($request->url());
		}
		
		return view('anchors.detail',['result' => $result,'rank' => $rank,'anchors' => $paginatedItems]);
	}
}
