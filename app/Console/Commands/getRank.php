<?php

namespace App\Console\Commands;

use App\Anchor;

use App\Http\Controllers\AnchorController;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

class getRank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getRank';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Google search result';

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
        $rows = DB::table('anchors')->where('status', MY_CRAWL_TODO)->get();
		
		foreach($rows as $row) {
			$id = $row->id;
			$results = AnchorController::scrape($id);
			$content = [];
			$getrank = [];
			foreach ($results as $key => $value) {
				$content[] = [$key,$value['title'],$value['link']];
				$getrank[] = ['anchors_id' => $id, 'rank' => $key, 'title' => $value['title'], 'description' => $value['description'], 'url' => $value['link']];
			}
			if (!empty($content)) {
				$this->info('Keyword ID: ' . $id);
				$headers = ['Rank ID', 'Title', 'URL'];
				Anchor::where('id', $id)->update(['status' => MY_CRAWL_URL_GENERATE,'result' => count($results)]);
				// Store search result into getrank table
				DB::table('getrank')->insert($getrank);
				
				$this->table($headers, $content);
				$this->info('* Using command: php artisan getAnchor to access each website');
			}
		}
    }
}
