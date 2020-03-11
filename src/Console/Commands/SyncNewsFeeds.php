<?php

namespace Flex360\Pilot\Console\Commands;

use Illuminate\Console\Command;
use Flex360\Pilot\Pilot\NewsFeed;

class SyncNewsFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync any newsfeeds';

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
        $feeds = config('news.feeds');

        foreach ($feeds as $feedData) {
            $feed = new NewsFeed($feedData);

            $feed->sync($this);
        }
    }
}
