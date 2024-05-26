<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Comment;
use App\Models\StatArticle;
use Carbon\Carbon;
use App\Mail\StatMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class StatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $commentCount = Comment::whereDate('created_at', Carbon::today())->count();
        $articleCount = StatArticle::all()->count();
        StatArticle::whereNotNull('id')->delete();
        // Log::alert($commentCount);
        Mail::to('misha_sidorenko228@mail.ru')->send(new StatMail($commentCount, $articleCount));
    }
}
