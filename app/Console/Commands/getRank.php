<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Anchor;

use App\Http\Controllers\AnchorController;

class getRank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rank:result {id}';

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
        $id = $this->argument('id');
		$keyword = DB::table('anchors')->where('id', $id)->value('keyword');
		$results = AnchorController::scrape($keyword);
		
		Anchor::where('id', $id)->update(array('status' => 2));
		
		$headers = ['Rank', 'Title', 'URL'];
		$content = array();
		foreach($results as $key => $value) {
			$content[] = [$key + 1,$value['title'],$value['link']];
		}
		$this->table($headers, $content);
		// $this->info('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36');
    }
}
