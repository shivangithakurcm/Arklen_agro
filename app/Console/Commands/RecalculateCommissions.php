<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;

class RecalculateCommissions extends Command
{
    protected $signature   = 'commissions:recalculate';
    protected $description = 'Sab members ka commission recalculate karo';

    public function handle()
    {
        // Sab reset karo
        Member::query()->update([
            'bv_left'               => 0,
            'bv_right'              => 0,
            'team_bv'               => 0,
            'new_joinee_income'     => 0,
            'direct_commission'     => 0,
            'level1_commission'     => 0,
            'level2_commission'     => 0,
            'direct_sponsor_income' => 0,
            'sponsor_income'        => 0,
            'team_income'           => 0,
            'total_income'          => 0,
            'balance'               => 0,
        ]);

        // Joining date order mein chalao (purane pehle)
        $members = Member::with('product')
                         ->orderBy('date_of_joining')
                         ->orderBy('id')
                         ->get();

        $bar = $this->output->createProgressBar($members->count());
        $bar->start();

        foreach ($members as $member) {
            $member->distributeCommission();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('✅ Sab members ka commission recalculate ho gaya!');
    }
}