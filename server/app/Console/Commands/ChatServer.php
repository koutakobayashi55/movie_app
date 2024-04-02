<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ChatServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:chat-server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $port_no = $this->argument('port_no');

        //--------------------------------------------------------------------------
        // リッスンポートで待ち受ける
        //--------------------------------------------------------------------------

        $ret = $this->manager->listen('localhost', $port_no);
        if($ret === false)
        {
            goto finish;   // リッスン失敗
        }

        //--------------------------------------------------------------------------
        // ノンブロッキングループ
        //--------------------------------------------------------------------------

        $timeout = config('const.cycle_driven_blocking_time');
        while(true)
        {
            // 周期ドリブン
            $ret = $this->manager->cycleDriven($timeout);
            if($ret === false)
            {
                goto finish;
            }
        }

    finish:
        // 全接続クローズ
        $this->manager->closeAll();
    }
}
