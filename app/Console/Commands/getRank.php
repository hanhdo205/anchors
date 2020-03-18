<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Anchor;

use Illuminate\Support\Facades\DB;

use App\Http\Controllers\AnchorController;

class getRank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getRank {rank}';

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
        $id = $this->argument('rank');
		$results = AnchorController::scrape($id);
		$status = DB::table('anchors')->where('id', $id)->value('status');
		
		if ($status < 3) $status = 2;
		
		$content = array();
		foreach($results as $key => $value) {
			$content[] = [$key + 1,$value['title'],$value['link']];
		}
		if(!empty($content)) {
			$headers = ['Rank', 'Title', 'URL'];
			Anchor::where('id', $id)->update(array('status' => $status,'result' => count($results)));
			$this->table($headers, $content);
		}
    }
}
