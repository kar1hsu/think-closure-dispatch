<?php

namespace Tests;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Karlhsu\ClosureDispatch\QueueInterface;

class ClosureDispatchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function createQueueMock()
    {
        return $this->createMock(QueueInterface::class);
    }

    public function testBasicDispatch()
    {
        $queue = $this->createQueueMock();
        $queue->expects($this->once())
            ->method('later')
            ->with(
                $this->equalTo(0),
                $this->equalTo(\Karlhsu\ClosureDispatch\Jobs\ClosureJob::class),
                $this->callback(function ($data) {
                    return isset($data['closure']) && is_string($data['closure']);
                }),
                $this->equalTo('default')
            )
            ->willReturn(true);
        $result = dispatch(function () {
            return true;
        }, $queue)->push();
        $this->assertTrue($result);
    }

    public function testDelayedDispatch()
    {
        $queue = $this->createQueueMock();
        $delay = 10;
        $queue->expects($this->once())
            ->method('later')
            ->with(
                $this->equalTo($delay),
                $this->equalTo(\Karlhsu\ClosureDispatch\Jobs\ClosureJob::class),
                $this->callback(function ($data) {
                    return isset($data['closure']) && is_string($data['closure']);
                }),
                $this->equalTo('default')
            )
            ->willReturn(true);
        $result = dispatch(function () {
            return true;
        }, $queue)->delay($delay)->push();
        $this->assertTrue($result);
    }

    public function testDelayedDispatchWithCarbon()
    {
        $queue = $this->createQueueMock();
        $delay = Carbon::now()->addSeconds(10);
        $expectedDelay = $delay->getTimestamp() - time();
        $queue->expects($this->once())
            ->method('later')
            ->with(
                $this->equalTo($expectedDelay),
                $this->equalTo(\Karlhsu\ClosureDispatch\Jobs\ClosureJob::class),
                $this->callback(function ($data) {
                    return isset($data['closure']) && is_string($data['closure']);
                }),
                $this->equalTo('default')
            )
            ->willReturn(true);
        $result = dispatch(function () {
            return true;
        }, $queue)->delay($delay)->push();
        $this->assertTrue($result);
    }

    public function testQueueDispatch()
    {
        $queue = $this->createQueueMock();
        $queueName = 'test-queue';
        $queue->expects($this->once())
            ->method('later')
            ->with(
                $this->equalTo(0),
                $this->equalTo(\Karlhsu\ClosureDispatch\Jobs\ClosureJob::class),
                $this->callback(function ($data) {
                    return isset($data['closure']) && is_string($data['closure']);
                }),
                $this->equalTo($queueName)
            )
            ->willReturn(true);
        $result = dispatch(function () {
            return true;
        }, $queue)->onQueue($queueName)->push();
        $this->assertTrue($result);
    }

    public function testFailedDispatch()
    {
        $queue = $this->createQueueMock();
        $queue->expects($this->once())
            ->method('later')
            ->with(
                $this->equalTo(0),
                $this->equalTo(\Karlhsu\ClosureDispatch\Jobs\ClosureJob::class),
                $this->callback(function ($data) {
                    return isset($data['closure']) && is_string($data['closure']);
                }),
                $this->equalTo('default')
            )
            ->willReturn(false);
        $result = dispatch(function () {
            return true;
        }, $queue)->push();
        $this->assertFalse($result);
    }
} 