<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use App\Models\Hotel;
use App\Models\Report;
use App\Models\User;
use Illuminate\View\View;

class AdminPanelController extends Controller
{
    public function index(): View
    {
        $pendingReportsCount = Report::query()->where('status', ReportStatus::Pending)->count();
        $hotelsCount = Hotel::query()->count();
        $usersCount = User::query()->count();

        return view('admin.index', compact('pendingReportsCount', 'hotelsCount', 'usersCount'));
    }
}
