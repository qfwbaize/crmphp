<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Workerman\Worker;

class Flow extends Command
{
    /**
     * @var int
     */
    protected $timer;
    /**
     * @var int|float
     */
    protected $interval = 2;
    protected function configure()
    {
        // 指令配置
        $this->setName('app\command\flow')
            ->addArgument('status', Argument::REQUIRED, 'start/stop/reload/status/connections')
            ->addOption('d', null, Option::VALUE_NONE, 'daemon（守护进程）方式启动')
            ->addOption('i', null, Option::VALUE_OPTIONAL, '多长时间执行一次')
            ->setDescription('开启/关闭/重启 定时任务');

    }
    protected function init(Input $input, Output $output)
    {
        global $argv;
        if ($input->hasOption('i'))
            $this->interval = floatval($input->getOption('i'));
        $argv[1] = $input->getArgument('status') ?: 'start';
        if ($input->hasOption('d')) {
            $argv[2] = '-d';
        } else {
            unset($argv[2]);
        }
    }


    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $this->init($input, $output);
        //创建定时器任务
        $task = new Worker();
        $task->count = 1;
        $task->onWorkerStart = [$this, 'start'];
        $task->runAll();
    }
    public function stop()
    {
        //手动暂停定时器
        \Workerman\Lib\Timer::del($this->timer);
    }
    public function start()
    {
        $last = time();
        $task = [6 => $last, 10 => $last, 30 => $last, 60 => $last, 180 => $last, 300 => $last];

        $this->timer = \Workerman\Lib\Timer::add($this->interval, function () use (&$task) {
            //每隔2秒执行一次
            try {
                $now = time();
                foreach ($task as $sec => $time) {
                    if ($now - $time >= $sec) {
                        //每隔$sec秒执行一次
                        $task[$sec] = $now;
                    }
                }
            } catch (\Throwable $e) {
            }
        });
    }
}
