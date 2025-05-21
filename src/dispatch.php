<?php

use Karlhsu\ClosureDispatch\DispatchWrapper;

if (!function_exists('dispatch')) {
    /**
     * 调度一个闭包到队列
     *
     * @param \Closure $job
     * @param mixed $queueInstance 可选，测试用
     * @return DispatchWrapper
     */
    function dispatch($job, $queueInstance = null)
    {
        return new DispatchWrapper($job, $queueInstance);
    }
} 