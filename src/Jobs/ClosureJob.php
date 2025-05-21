<?php
namespace Karlhsu\ClosureDispatch\Jobs;

use think\queue\Job;

class ClosureJob
{
    public function fire(Job $job, $data)
    {
        $closure = unserialize($data['closure']);
        if ($closure instanceof \Closure) {
            $closure();
        }

        $job->delete();
    }
}