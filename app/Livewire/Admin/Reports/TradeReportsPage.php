<?php

namespace App\Livewire\Admin\Reports;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class TradeReportsPage extends Component
{
    public function render(): View
    {
        return view('livewire.admin.reports.trade-reports-index');
    }
}