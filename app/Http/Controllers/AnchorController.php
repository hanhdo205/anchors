<?php

namespace App\Http\Controllers;

use App\Anchor;

use Illuminate\Http\Request;

use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Http;

class AnchorController extends Controller
{
    /**
     * Display a listing of the keywords.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $anchors = Anchor::orderByRaw('id DESC')->paginate(10);
        return view('welcome', compact('anchors', $anchors));
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

        return redirect('/');
    }
    
    /**
     * Removes all html tags and the contents within them unlike strip_tags which only removes the tags themselves.
     *
     */
    public static function strip_tags_content($text, $tags = '', $invert = false)
    {
        //removes <br> often found in google result text, which is not handled below
        $text = str_ireplace('<br>', '', $text);
     
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);
     
        if (is_array($tags) and count($tags) > 0) {
            //if invert is false, it will remove all tags except those passed a
            if ($invert == false) {
                return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
            //if invert is true, it will remove only the tags passed to this function
            } else {
                return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text);
            }
            //if no tags were passed to this function, simply remove all the tags
        } elseif ($invert == false) {
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
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36',
        ])->get($url);
     
        return $response;
    }
    
    
    /**
     * Scrape Google search results.
     *
     * @param $keyword
     * @return array
     */
    public static function scrape($id)
    {
        include_once(app_path() . '\includes\simple_html_dom.php');
        $keyword = DB::table('anchors')->where('id', $id)->value('keyword');
        
        //Obtain the first page html with the formated url
        $data = self::file_get_contents_curl(MY_CRAWL_INIT . urlencode(str_replace(' ', '+', $keyword)));
         
        /*
        create a simple_html_dom object from the retreived string
        you could also perform file_get_html("http://...") instead of
        file_get_contents_curl above, but it wouldn't change the default
        User-Agent
        */
     
        $html = str_get_html($data);
         
        $results = [];
         
        foreach ($html->find('div.srg div.g') as $g) {
            /*
            each search results are in a list item with a class name 'g'
            we are seperating each of the elements within, into an array

            Titles are stored within <h3><a...>{title}</a></h3>
            Links are in the href of the anchor contained in the <h3>...</h3>
            Summaries are stored in a div with a classname of 's'
            */
         
            $s = $g->find('span.st', 0);
            $a = $g->find('a', 0);
            $t = $a->find('h3', 0);
            $results[] = ['title' => strip_tags($t->innertext),
                'link' => $a->href,
                'description' => $s ? self::strip_tags_content($s->innertext) : ''];
        }
        
        //Cleans up the memory
        $html->clear();
        
        return $results;
    }
	
	public static function innerHTML($node)
    {
        $ret = '';
        foreach ($node->childNodes as $node) {
            $ret .= $node->ownerDocument->saveHTML($node);
        }
        return $ret;
    }
    
    /**
     * Scrape Google search results.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function result(Request $request)
    {
        $keyword = $request->q;
		
		$rows = DB::table('getrank')
            ->join('anchors', 'getrank.anchors_id', '=', 'anchors.id')
			->where('anchors.keyword', $keyword)
            ->select('getrank.anchors_id', 'getrank.rank_id', 'getrank.title', 'getrank.url', 'anchors.status')
            ->get();

		$results = [];
		
		if(!$rows->count()) 
		return abort(404);
				
		$status = $rows[0]->status;

		foreach($rows as $row) {
			$results[] = ['title' => $row->title,
                'link' => $row->url];
		}
		        
        return view('anchors.result', compact(['results','keyword','status']));
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
				
		$rows = DB::table('getanchor')
            ->join('getrank', 'getanchor.getrank_id', '=', 'getrank.rank_id')
            ->join('anchors', 'getrank.anchors_id', '=', 'anchors.id')
			->where('anchors.keyword', $keyword)
			->where('getrank.rank', $rank)
            ->select('getanchor.*', 'getrank.url', 'getrank.title', 'getrank.description', 'anchors.status')
            ->get();
			
		if(!$rows->count()) 
		return abort(404);
        
        //Array that will contain our extracted links.
        $anchors = [];
		$result = ['link' => $rows[0]->url,'title' => $rows[0]->title,'description' => $rows[0]->description,];

        //Loop through the DOMNodeList.
        //We can do this because the DOMNodeList object is traversable.
		foreach ($rows as $row) {
		
			//Add the link to our $anchors array.
			$anchors[] = [
				'text' => $row->anchor_text,
				'url' => $row->anchor_url,
				'type' => $row->anchor_type,
			];
					
			// Get current page form url e.x. &page=1
			$currentPage = LengthAwarePaginator::resolveCurrentPage();
 
			// Create a new Laravel collection from the array data
			$itemCollection = collect($anchors);
 
			// Define how many items we want to be visible in each page
			$perPage = 10;
 
			// Slice the collection to get the items to display in current page
			$currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
 
			// Create our paginator and pass it to the view
			$paginatedItems= new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);
 
			// set url path for generted links
			$paginatedItems->setPath($request->url());
		}
        
        return view('anchors.detail', ['result' => $result,'rank' => $rank,'anchors' => $paginatedItems]);
    }
}
