# ThinkPHP Closure Dispatch

为 ThinkPHP 提供类似 Laravel 的闭包队列调度功能。

## 安装

```bash
composer require karlhsu/think-closure-dispatch
```

## 要求

- PHP >= 7.4
- ThinkPHP >= 6.0
- ThinkPHP Queue >= 3.0

## 使用方法

### 基本使用

```php
use function Karlhsu\ClosureDispatch\dispatch;

// 基本使用
dispatch(function() {
    echo "Hello World";
})->push();

// 延迟执行
dispatch(function() {
    echo "延迟执行的任务";
})->delay(60)->push();  // 60秒后执行

// 指定队列
dispatch(function() {
    echo "在指定队列中执行的任务";
})->onQueue('emails')->push();

// 使用 Carbon 设置延迟时间
use Carbon\Carbon;

dispatch(function() {
    echo "在指定时间执行的任务";
})->delay(Carbon::now()->addMinutes(30))->push();
```

### 配置

确保项目中已安装并配置了 `topthink/think-queue` 包。在 `config/queue.php` 中配置队列连接信息：

```php
<?php
return [
    'default'     => 'sync',
    'connections' => [
        'sync'     => [
            'type' => 'sync',
        ],
        'database' => [
            'type'       => 'database',
            'queue'      => 'default',
            'table'      => 'jobs',
            'connection' => null,
        ],
        'redis'    => [
            'type'       => 'redis',
            'queue'      => 'default',
            'host'       => '127.0.0.1',
            'port'       => 6379,
            'password'   => '',
            'select'     => 0,
            'timeout'    => 0,
            'persistent' => false,
        ],
    ],
    'failed'      => [
        'type'  => 'none',
        'table' => 'failed_jobs',
    ],
];
```

## 最佳实践

1. 对于复杂的任务，建议创建独立的 Job 类
2. 使用队列时注意设置合理的重试次数和超时时间
3. 建议在闭包中捕获异常并记录日志
4. 对于需要传递大量数据的任务，建议使用数据库或缓存存储数据，在闭包中只传递数据ID

示例：
```php
// 不推荐
dispatch(function() use ($largeData) {
    // 处理大量数据
})->push();

// 推荐
$dataId = saveToDatabase($largeData);
dispatch(function() use ($dataId) {
    $data = getFromDatabase($dataId);
    // 处理数据
})->push();
```

## 注意事项

1. 闭包中使用的变量需要是可序列化的
2. 如果闭包中使用了类，确保这些类可以被正确序列化
3. 建议在闭包中只处理业务逻辑，避免使用外部依赖

## 测试

```bash
composer test
```

## License

MIT 