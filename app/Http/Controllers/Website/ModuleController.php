<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ModuleController extends Controller
{
    public function index()
    {
        return  view('pages/access/index');
    }

    function getMenuList(Request $request)
    {
        $roleList = Role::get();
        $c = User::select('access')->where('id',  $request->userId)->first();
        $MList = $c ? $c->access : '';
        $role = 'Custom';
        if ($request->role) {
            $b = Role::where('id',  $request->role)->first();
            if ($b) {
                $MList = $b ? $b->menuAccess : '';
                $role = $b ? $b->name : 'Custom';
            }
        } else {
            $a = Role::whereRaw("menuAccess = '$MList'")->first();
            $role = $a ? $a->name : 'Custom';
        }


        $menuList = Module::whereNotIn('route', ['', '#'])->orderBy('group_id')->orderBy('list_no')->where('isactive', 1)->get();
        $accessList = Module::whereNotIn('route', ['', '#'])->whereIn('id', explode(',', $MList))->where('isactive', 1)->orderBy('id', 'asc')->get();


        return view('pages/access/list', compact('accessList', 'menuList', "role", 'roleList'));
        // return response()->json(["AccessList" => $AccessList, "menuList" =>$menuList], 200);
    }

    function getUserList(Request $request)
    {
        $userList = User::select([
            'm_users.id',
            'm_employee.fullname',
            'm_employee.nik',
        ])
            ->join('m_employee', 'm_employee.id', '=', 'm_users.fk_employee')
            ->where('m_users.id', '!=', Auth::user()->id)
            ->where('m_users.id', '!=', 1)
            ->where(function ($query) use ($request) {
                $query->where('m_employee.fullname', 'like', '%' . $request->params . '%')
                    ->orWhere('m_employee.nik', 'like', '%' . $request->params . '%');
            })
            ->limit(10)
            ->get();
        return response()->json(["message" => 'success', "data" => $userList], 200);
    }

    function saveAccess(Request $request)
    {
        $access = [];
        $header = [];
        if ($request->sArray) {
            foreach ($request->sArray as $i) {
                if (!in_array($i['ids'], $access)) {
                    $access[] = $i['ids'];
                }
                if (!in_array($i['idg'], $header)) {
                    $header[] = $i['idg'];
                }
            }
        }
        sort($access);

        $update = User::where('id', strip_tags($request->userId))->update([
            "access" => implode(',', $access)
        ]);
        if ($update) {
            return response()->json(["message" => 'success'], 200);
        }
        return response()->json(["error" => 'error'], 400);
    }
}
