<?php
use GO\Scheduler;

// ... configure the scheduled jobs (see below) ...

function testJob($receiver){

    // Create a new scheduler
    $scheduler = new Scheduler();

    $scheduler->php(base_url("general/cron"))->everyMinute();

    // $scheduler->call(
    //     function ($receiver, $data,$role){
    //         //return file_put_contents("job.log","It worked");
    //         return fire_email("Hello",'Hello World',$receiver);
    //     },
    //     [
    //         $receiver ,
    //         [
    //             'first_name' => 'John',
    //             'last_name' => 'Doe',
    //         ],
    //         'Admin'
    //     ],
    //     'JOB-'.time()
    // )->everyMinute();

   $scheduler->run();
}