<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index()
    {
        return view('pages/activity-log/index');
    }

    public function getList(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start');
        $length = $request->input('length');
        $searchValue = $request->input('search.value');

        $query = ActivityLog::query()
            ->with('user:id,display_name')
            ->where('klienfk', Auth::user()->klienfk);

        if (! empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('model_type', 'like', "%{$searchValue}%")
                    ->orWhere('action', 'like', "%{$searchValue}%");
            });
        }

        $recordsTotal = $query->count();

        $data = $query->orderBy('id', 'desc')
            ->skip($start)
            ->take($length)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                    'user' => $log->user->display_name ?? 'System',
                    'action' => $log->action,
                    'model' => class_basename($log->model_type),
                    'model_id' => $log->model_id,
                    'changes' => $log->changes,
                ];
            });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data,
        ]);
    }
}
