<?php

namespace App\Http\Controllers\Website;


use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\JobLevel;
use App\Models\JobType;
use App\Models\MarialStatus;
use App\Models\Ptkp;
use App\Models\Religion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\error;

class EmployeesController extends Controller
{
    public function index()
    {
        return view('pages/employees/index');
    }

    public function getEmployees()
    {
        $data = Employee::getEmployees();
        return response()->json(["status" => true, "message" => "success", "data" => $data]);
    }

    public function getList(Request $request)
    {
        // Ambil parameter dari DataTables
        $draw = $request->input('draw');
        $start = $request->input('start'); // offset
        $length = $request->input('length'); // jumlah data per halaman
        $searchValue = $request->input('search.value'); // nilai pencarian

        // Query utama
        $query = Employee::select([
            'm_employee.id',
            'm_employee.nik',
            'm_employee.fullname',
            'm_employee.gender',
            DB::raw("coalesce(m_designation.name, '-') as designation"),
            DB::raw("coalesce(m_department.name, '-') as department"),
            DB::raw("coalesce(m_jobtype.name, '-') as jobtype"),
            DB::raw("CASE WHEN m_users.id IS NOT NULL THEN '1' ELSE '-' END as isUser"),
        ])
            ->leftJoin('m_jobtype', "m_jobtype.id", "m_employee.fk_jobtype")
            ->leftJoin('m_designation', "m_designation.id", "m_employee.fk_designation")
            ->leftJoin('m_department', "m_department.id", "m_designation.fk_department")
            ->leftJoin('m_users', "m_users.fk_employee", "m_employee.id");

        // Total data tanpa filter
        $recordsTotal = $query->count();

        // Jika ada pencarian
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('m_employee.nik', 'like', "%{$searchValue}%")
                    ->orWhere('m_employee.fullname', 'like', "%{$searchValue}%")
                    ->orWhere('m_designation.name', 'like', "%{$searchValue}%")
                    ->orWhere('m_department.name', 'like', "%{$searchValue}%")
                    ->orWhere('m_jobtype.name', 'like', "%{$searchValue}%");
            });
        }

        // Total data setelah filter
        $recordsFiltered = $query->count();

        // Pagination (offset dan limit)
        $data = $query
            ->skip($start)
            ->take($length)
            ->get();

        // Format respons DataTables
        $response = [
            "draw" => intval($draw),
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data->map(function ($item) {
                return [
                    "id" => $item->id,
                    "nik" => $item->nik,
                    "fullname" => $item->fullname,
                    "gender" => $item->gender,
                    "designation" => $item->designation,
                    "department" => $item->department,
                    "jobtype" => $item->jobtype,
                    "isUser" => $item->isUser,
                ];
            }),
        ];

        return response()->json($response, 200);
    }

    public function editEmployee($idn)
    {
        $decodedId = base64_decode($idn);
        $employee  = Employee::select([
            'm_employee.fullname',
            'm_designation.name as designation',
        ])
            ->leftJoin('m_designation', "m_designation.id", "m_employee.fk_designation")
            ->where('m_employee.id', $decodedId)
            ->first();
        return view('pages/employees/edit', compact('idn', 'employee'));
    }

    public function profile($idn)
    {
        $id = base64_decode($idn);
        $data = Employee::where('id', $id)->first();
        $ms = MarialStatus::get();
        $rl = Religion::get();
        return view('pages/employees/content/profile', compact('data',  'ms', 'rl',  'id'));
    }
    public function employment($idn)
    {
        $id = base64_decode($idn);
        $data = Employee::where('id', $id)->first();
        $jl = JobLevel::get();
        $jt = JobType::get();
        $ds = Designation::get();
        $ptkp = Ptkp::get();
        return view('pages/employees/content/employment', compact('data', 'jl', 'jt', 'ds',  'ptkp', 'id'));
    }

    public function contract($idn)
    {
        $id = base64_decode($idn);
        $data = Employee::where('id', $id)->first();
        return view('pages/employees/content/contract', compact('idn', 'data'));
    }

    public function getEmployeeContract($idn)
    {
        $id = base64_decode($idn);
        $data = EmployeeContract::where('fk_employee', $id)->orderBy('id', 'desc')->get();
        return response()->json(["message" => 'success', 'data' => $data], 200);
    }

    public function addContract($idn)
    {
        $id = base64_decode($idn);
        $cekNull = EmployeeContract::where('fk_employee', $id)
            ->where("datestart", null)
            ->where("dateend", null)
            ->where("contracttype", null)
            ->where("contractnature", null)
            ->where("isrenewal", 0)
            ->first();

        if (!$cekNull) {
            $insertID = EmployeeContract::insertGetID([
                'fk_employee' => $id
            ]);
            return response()->json(["message" => 'success', 'data' => ['insertID' => $insertID]], 200);
        }
        return response()->json(["error" => 'Please check contract list before add new!', 'data' => null], 400);
    }

    public function assignment($idn)
    {
        $id = base64_decode($idn);
        $data = Employee::where('id', $id)->first();
        return view('pages/employees/content/assignment', compact('idn', 'data'));
    }

    public function statutory($idn)
    {
        $id = base64_decode($idn);
        $data = Employee::where('id', $id)->first();
        $ptkp = Ptkp::orderBy('name')->get();
        return view('pages/employees/content/statutory', compact('idn', 'data', 'ptkp'));
    }

    public function saveStatutory(Request $request, $idn)
    {
        $id = base64_decode($idn);
        Employee::where('id', $id)->update($request->only([
            'npwp', 'fk_ptkp', 'bpjs_kesehatan_no', 'bpjs_tk_no',
        ]));
        return response()->json(['message' => 'success'], 200);
    }

    public function salary($idn)
    {
        $id = base64_decode($idn);
        $data = Employee::where('id', $id)->first();
        return view('pages/employees/content/salary', compact('idn', 'data'));
    }

    public function saveSalary(Request $request, $idn)
    {
        $request->validate(['gajipokok' => 'required|numeric|min:0']);
        $id = base64_decode($idn);
        Employee::where('id', $id)->update(['gajipokok' => $request->gajipokok]);
        return response()->json(['message' => 'success'], 200);
    }

    public function bank($idn)
    {
        $id = base64_decode($idn);
        $data = Employee::where('id', $id)->first();
        $banks = Bank::get();
        return view('pages/employees/content/bank', compact('idn', 'data', 'banks'));
    }
}
