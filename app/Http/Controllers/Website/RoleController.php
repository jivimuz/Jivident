<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return view('pages/role/index');
    }

    public function getList(Request $request)
    {
        $search = $request->input('search.value');

        $query = Role::query();
        if (! empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        $recordsTotal = Role::count();
        $recordsFiltered = $query->count();

        $data = $query->orderBy('id')
            ->skip((int) $request->input('start', 0))
            ->take((int) $request->input('length', 10))
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'total_modules' => count(array_filter(explode(',', $role->menuAccess))),
                ];
            });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function add()
    {
        $menuList = $this->moduleList();

        return view('pages/role/form', [
            'role' => null,
            'menuList' => $menuList,
            'checkedIds' => [],
        ]);
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $menuList = $this->moduleList();

        return view('pages/role/form', [
            'role' => $role,
            'menuList' => $menuList,
            'checkedIds' => array_filter(explode(',', $role->menuAccess)),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Role::create([
            'name' => $request->name,
            'menuAccess' => $this->moduleIdsFromRequest($request),
        ]);

        return response()->json(['message' => 'success'], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Role::findOrFail($id)->update([
            'name' => $request->name,
            'menuAccess' => $this->moduleIdsFromRequest($request),
        ]);

        return response()->json(['message' => 'success'], 200);
    }

    public function destroy($id)
    {
        Role::findOrFail($id)->delete();

        return response()->json(['message' => 'success'], 200);
    }

    private function moduleList()
    {
        return Module::whereNotIn('route', ['', '#'])
            ->where('isactive', 1)
            ->orderBy('group_id')
            ->orderBy('list_no')
            ->get();
    }

    private function moduleIdsFromRequest(Request $request): string
    {
        $ids = array_filter((array) $request->input('modules', []));
        sort($ids);

        return implode(',', $ids);
    }
}
