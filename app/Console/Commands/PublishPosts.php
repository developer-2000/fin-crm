<?php

namespace App\Console\Commands;

use App\Models\CronTasks;
use App\Models\Variables;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

use App\Models\Order;
use App\Models\CallProcessing;
use App\Models\Campaign;
use App\Models\CallProgressLog;
use \Log;

class PublishPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish_posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish posts due to schedule';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){

        Post::where([['publish_at', '<=', Carbon::now()], ['publish_complete', 0]])->update(['publish_complete', 1]);
        $this->info('All current posts are published successfully!');
    }
}
