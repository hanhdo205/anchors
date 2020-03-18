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
        
        if ($status < 3) {
            $status = 2;
        }
        
        $content = [];
        foreach ($results as $key => $value) {
            $content[] = [$key,$value['title'],$value['link']];
        }
        if (!empty($content)) {
            $this->info('Keyword ID: ' . $id);
            $headers = ['Rank ID', 'Title', 'URL'];
            Anchor::where('id', $id)->update(['status' => $status,'result' => count($results)]);
            $this->table($headers, $content);
            $this->info('* Using command: php artisan getAnchor {Keyword ID} {Rank ID} to access each website');
        }
    }
}
