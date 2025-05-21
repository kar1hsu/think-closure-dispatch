<?php

namespace Karlhsu\ClosureDispatch;

interface QueueInterface
{
    /**
     * 延迟执行任务
     *
     * @param int $delay 延迟时间（秒）
     * @param string $job 任务类名
     * @param array $data 任务数据
     * @param string $queue 队列名称
     * @return bool
     */
    public function later($delay, $job, $data = [], $queue = 'default');
} 