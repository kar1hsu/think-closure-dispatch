<?php
namespace Karlhsu\ClosureDispatch;

use Carbon\Carbon;
use Opis\Closure\SerializableClosure;
use think\facade\Queue as QueueFacade;

class DispatchWrapper
{
    protected $job;
    protected int $delay = 0;
    protected string $queue = 'default';
    protected $queueInstance;

    public function __construct($job, $queueInstance = null)
    {
        $this->job = $job;
        $this->queueInstance = $queueInstance;
    }

    public function delay($delay): self
    {
        if ($delay instanceof \DateTimeInterface) {
            $this->delay = $delay->getTimestamp() - time();
        } else {
            $this->delay = intval($delay);
        }
        return $this;
    }

    public function onQueue(string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    public function push(): bool
    {
        $queue = $this->queueInstance ?: app('queue');
        if ($this->job instanceof \Closure) {
            $closure = new SerializableClosure($this->job);
            $result = $queue->later(
                $this->delay,
                \Karlhsu\ClosureDispatch\Jobs\ClosureJob::class,
                ['closure' => serialize($closure)],
                $this->queue
            );
            return $result !== false;
        }
        return false;
    }
}