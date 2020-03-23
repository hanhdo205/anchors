<?php

namespace App\Console\Commands;

use App\Anchor;

use App\Http\Controllers\AnchorController;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

class getAnchor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getAnchor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get Description, Page Title, Anchorlink';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        set_time_limit(8000000);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $rows = DB::table('getrank')
        ->join('anchors', function ($join) {
            $join->on('getrank.anchors_id', '=', 'anchors.id')
                 ->where('anchors.status', MY_CRAWL_URL_GENERATE);
        })
        ->get();
        $arr_access = [];
        $getanchor = [];

        foreach ($rows as $row) {
            $rank = $row->rank;
            $rank_id = $row->rank_id;
            $id = $row->anchors_id;

            $arr_access[] = $rank;
            $rowstatus = $row->status;
            $rowresult = $row->result;
            
            if ($rowresult && $rank >= $rowresult) {
                $this->info('Out of Range');
                return;
            }
            
            //Get the page's HTML source using file_get_contents.
            
            try {
                $html = AnchorController::file_get_contents_curl($row->url);
            } catch (Exception $e) {
                $this->info('Can not get data from ' . $row->url);
            }
            
            if ($html->successful()) {

                //Instantiate the DOMDocument class.
                $htmlDom = new \DOMDocument;

                //Parse the HTML of the page using DOMDocument::loadHTML
                @$htmlDom->loadHTML($html);

                //Extract the links from the HTML.
                $links = $htmlDom->getElementsByTagName('a');

                //Array that will contain our extracted links.
                $anchors = [];

                //Loop through the DOMNodeList.
                //We can do this because the DOMNodeList object is traversable.
                if ($links->length > 1) {
                    foreach ($links as $link) {

                        //Get the link text.
                        $linkText = AnchorController::innerHTML($link);
                        $linkType = 'Text';
                    
                        if (preg_match('/<img/', $linkText)) {
                            $linkType = 'Img';
                            $doc = new \DOMDocument();
                            @$doc->loadHTML($linkText);
                            $xpath = new \DOMXPath($doc);
                            $linkText = $xpath->evaluate("string(//img/@src)");
                        }
                    
                        //Get the link in the href attribute.
                        $linkHref = $link->getAttribute('href');

                        //If the text is empty, skip it and don't
                        //add it to our $anchors array
                        if (strlen(trim($linkText)) == 0) {
                            continue;
                        }
                    
                        //If the link is empty, skip it and don't
                        //add it to our $anchors array
                        if (strlen(trim($linkHref)) == 0) {
                            continue;
                        }

                        //Skip if it is a hashtag / anchor link.
                        if ($linkHref[0] == '#') {
                            continue;
                        }

                        //Add the link to our $anchors array.
                        $anchors[] = [
                            'text' => strip_tags($linkText),
                            'url' => $linkHref,
                            'type' => $linkType,
                        ];
                    }
                
                    
                    $headers = ['ID', 'Anchor Text', 'Anchor Type', 'Anchor URL'];
                    $content = [];
                
                    foreach ($anchors as $key => $value) {
                        $content[] = [$key,$value['text'],$value['type'],$value['url']];
                        $getanchor[] = ['getrank_id' => $rank_id, 'anchor_text' => $value['text'], 'anchor_type' => $value['type'], 'anchor_url' => $value['url']];
                    }
                    $this->info('URL: ' . $row->url);
                    $this->info('Title: ' . $row->title);
                    $this->info('Description: ' . $row->description);
                    $this->table($headers, $content);
                } else {
                    $this->info('Can not get data from ' . $row->url);
                }
            } else {
                $this->info('Can not get data from ' . $row->url);
            }
            
            switch (true) {
                case (count($arr_access) >= $rowresult):
                    $rowstatus = MY_CRAWL_DONE;
                    break;
                default:
                    $rowstatus = MY_CRAWL_ANCHOR_GENERATE;
                    break;
            }
            
            Anchor::where('id', $id)->update(['status' => $rowstatus]);
        }
        DB::table('getanchor')->insert($getanchor);
    }
}
