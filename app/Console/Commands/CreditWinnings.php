<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CreditWinnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'winnings:credit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Credit users with viable winnings ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        WalletTransaction::unsettled()
            ->where('viable_date', '<=', Carbon::now()->subHours(config('trivia.staking.hours_before_withdrawal')))
            ->chunkById(500, function ($transactions) {
                foreach ($transactions as $transaction) {
                    $transaction->wallet->update(['withdrawable_balance' => ($transaction->wallet->withdrawable_balance + $transaction->amount)]);
                }
                $transactions->each->update(['settled_at' => now()]);
            }, $column = 'id');
        return 0;
    }
}
