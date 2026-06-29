<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('modul'))   $query->modul($request->modul);
        if ($request->filled('aksi'))    $query->where('aksi', $request->aksi);
        if ($request->filled('user_id')) $query->olehUser($request->user_id);
        if ($request->filled('dari') || $request->filled('sampai'))
            $query->tanggal($request->dari, $request->sampai);
        if ($request->filled('keyword'))
            $query->where('deskripsi', 'like', '%'.$request->keyword.'%');

        $logs      = $query->paginate(20)->withQueryString();
        $users     = User::orderBy('name')->get();
        $modulList = ActivityLog::distinct()->pluck('modul')->sort()->values();
        $aksiList  = ActivityLog::distinct()->pluck('aksi')->sort()->values();

        return view('activity-log.index', compact('logs', 'users', 'modulList', 'aksiList'));
    }

    public function destroy(ActivityLog $activityLog)
    {
        $activityLog->delete();
        return back()->with('success', 'Log berhasil dihapus.');
    }

    public function destroyAll()
    {
        ActivityLog::truncate();
        return back()->with('success', 'Semua log berhasil dihapus.');
    }
}