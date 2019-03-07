<?php

namespace App\Console;

use App\Console\Commands\SomeProblem;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
        //\App\Console\Commands\Inspire::class,
        \App\Console\Commands\NightShift::class,
        \App\Console\Commands\GetCalls::class,
        \App\Console\Commands\AuditOperator::class,
        \App\Console\Commands\CheckOnlineUser::class,
        \App\Console\Commands\ResetTime::class,
        \App\Console\Commands\SocketServer::class,
        \App\Console\Commands\GetOrdersInProcessing::class,
        \App\Console\Commands\GetOrderToday::class,
        \App\Console\Commands\ColdCalls::class,
        \App\Console\Commands\AddCalls::class,
        \App\Console\Commands\AddLearningCalls::class,
        \App\Console\Commands\WeekTransaction::class,
        \App\Console\Commands\MonthTransaction::class,
        \App\Console\Commands\SomeProblem::class,
        \App\Console\Commands\ExhangeRates::class,
        \App\Console\Commands\VietnamWardsLoad::class,
        \App\Console\Commands\WeFastTracking::class,
        \App\Console\Commands\CronVietnam::class,
        \App\Console\Commands\NovaposhtaTracking::class,
        \App\Console\Commands\NovaposhtaFinalTracking::class,
        \App\Console\Commands\ViettelTracking::class,
        \App\Console\Commands\MeasoftCreateOrders::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('nightShift')->withoutOverlapping();
        $schedule->command('getCalls')->withoutOverlapping();
        $schedule->command('addCalls')->withoutOverlapping();
        $schedule->command('addLearningCalls')->withoutOverlapping();

        $schedule->command('resetTime')->dailyAt('00:00');
        $schedule->command('online')->withoutOverlapping();


        $schedule->command('auditOperator')->withoutOverlapping();
        $schedule->command('auditOperatorOwners')->withoutOverlapping();
        
        $schedule->command('cold_calls')->withoutOverlapping();

        $schedule->command('publish_posts')->withoutOverlapping();
        $schedule->command('exchange_rates')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
