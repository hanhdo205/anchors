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
    protected $signature = 'getAnchor {keyword} {rank}';

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
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $rank = $this->argument('rank');
        $id = $this->argument('keyword');
        
        $rows = DB::table('anchors')
        ->select(['status', 'result', 'access'])
        ->find($id);
        
        $arr_access = explode(',', $rows->access);
        if (!in_array($rank, $arr_access)) {
            array_push($arr_access, $rank);
        }
        $arr_access = array_filter($arr_access, 'strlen');
        $rowaccess = implode(',', $arr_access);
        $rowstatus = $rows->status;
        $rowresult = $rows->result;
        
        if ($rowresult && $rank >= $rowresult) {
            $this->info('Out of Range');
            return;
        }
    
        $results = AnchorController::scrape($id);
        $result = $results[$rank];
        
        //Get the page's HTML source using file_get_contents.
        $html = AnchorController::file_get_contents_curl($result['link']);

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
            }
            $this->info('URL: ' . $result['link']);
            $this->info('Title: ' . $result['title']);
            $this->info('Description: ' . $result['description']);
            $this->table($headers, $content);
        } else {
            $this->info('Can not get data from ' . $result['link']);
        }
        
        switch (true) {
            case (count($arr_access) >= $rowresult):
                $rowstatus = 4;
                break;
            default:
                $rowstatus = 3;
                break;
        }
        
        Anchor::where('id', $id)->update(['status' => $rowstatus,'access' => $rowaccess]);
    }
}
