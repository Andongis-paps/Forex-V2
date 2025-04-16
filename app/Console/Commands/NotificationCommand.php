<?php

namespace App\Console\Commands;

use App\Helpers\AutorunNotification;
use Illuminate\Console\Command;

class NotificationCommand extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:node';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-run Node';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        AutorunNotification::autorun();
        return 0;
    }
}
