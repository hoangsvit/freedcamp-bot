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
        $params = [
            'limit' => 200,
            'offset' => 0,
            'filter' => [
                'created_by_id' => config('app.freedcamp.created_by_id'),
                'created_date' => [7],
                // 'status_id' => [9683]
            ],
            'sort' => ['priority' => 'asc'],
        ];

        $response = sendFreedcampRequest($params, 'tasks', 'get');

        if ($response) {
            $tasks = $response['data']['tasks'];
            logger('Freedcamp tasks', $tasks);
            foreach ($tasks as $task) {
                if (!Str::of($task['title'])->startsWith('#')) {
                    $this->info("Processing task {$task['title']}");
                }
            }
        }
    }
}
