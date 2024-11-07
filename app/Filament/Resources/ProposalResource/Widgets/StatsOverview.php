<?php

namespace App\Filament\Resources\ProposalResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Proposal;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProposals = Proposal::count();
        $pendingProposals = Proposal::where('proposal_status', 'PENDING')->count();
        $approvedProposals = Proposal::where('proposal_status', 'APPROVED')->count();
        $rejectedProposals = Proposal::where('proposal_status', 'REJECTED')->count();

        return [
            Stat::make('Total Proposals', $totalProposals),
            Stat::make('Pending Proposals', $pendingProposals),
            Stat::make('Approved Proposals', $approvedProposals),
            Stat::make('Rejected Proposals', $rejectedProposals),
        ];
    }
}

