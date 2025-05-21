<?php
namespace Karlhsu\ClosureDispatch\Jobs;

use think\queue\Job;
use Opis\Closure\SerializableClosure;

class ClosureJob
{
    public function fire(Job $job, $data)
    {
        try {
            $closure = unserialize($data['closure']);
            if ($closure instanceof SerializableClosure) {
                $closure = $closure->getClosure();
            }
            
            if ($closure instanceof \Closure) {
                $closure();
            }
            
            $job->delete();
        } catch (\Throwable $e) {
            // 记录错误日志
            if (function_exists('trace')) {
                trace($e->getMessage(), 'error');
            }
            
            // 如果任务失败，可以选择重试或删除
            if ($job->attempts() > 3) {
                $job->delete();
            } else {
                $job->release(3);
            }
        }
    }

    public function failed($data)
    {
        // 任务最终失败时的处理
        if (function_exists('trace')) {
            trace('Queue job failed: ' . json_encode($data), 'error');
        }
    }
}