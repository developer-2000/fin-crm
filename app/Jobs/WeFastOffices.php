<?php

namespace App\Jobs;

use App\Models\Api\WeFast\WeFastOffice;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class WeFastOffices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $newOffices;

    private $oldOffices;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($newOffices, $oldOffices)
    {
        $this->newOffices = $newOffices;
        $this->oldOffices = $oldOffices;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        foreach ($this->newOffices as $item) {
//            if (isset($this->oldOffices[$this->newOffices['ward_code']])) {
//                WeFastOffice::where('ward_code', $this->newOffices['ward_code'])->update(['active' => 1]);
//            } else {
//                WeFastOffice::create($this->newOffices);
//            }
//        }
    }
}
