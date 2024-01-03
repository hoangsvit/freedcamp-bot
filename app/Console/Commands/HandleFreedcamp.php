<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Jobs\FreedcampJob;

class HandleFreedcamp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'handle:freedcamp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Freedcamp';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $params = json_decode(config('app.freedcamp.params'));
        $response = sendFreedcampRequest($params, 'tasks', 'get');

        if ($response) {
            $tasks = $response['data']['tasks'];

            foreach ($tasks as $task) {
                if (!Str::of($task['title'])->startsWith('#')) {

                    $id = $task['id'];
                    $title = "#{$id} - {$task['title']}";
                    $this->info("Processing task {$title}");

                    sendFreedcampRequest([], 'tasks/' . $id, 'post', ['title' => $title]);
                }
            }
        }
    }
}
