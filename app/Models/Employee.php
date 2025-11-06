<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Employee extends Model
{
    use HasFactory;
    protected $table = "m_employee";
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'fk_employee', 'id');
    }

    public static function getEmployees()
    {
        $data = DB::select(
            "SELECT t1.*, t2.name as job_level_name, t2.level as job_level, t3.name as job_type_name, t4.*,
            t5.name as study_name, t5.grade as study_grade, t6.name as marital_status, t7.name as religion_name, t8.name as ptkp_name, t8.nominal as ptkp_nominal,
            t9.username as username, t9.email as email
            FROM m_employee t1
            LEFT JOIN m_joblevel t2 on t2.id=t1.fk_joblevel
            LEFT JOIN m_jobtype t3 on t3.id=t1.fk_jobtype
            LEFT JOIN (
                SELECT t1.id as designation_id, t1.name as designation_name, t2.name as department_name, t2.code as department_code
                FROM m_designation t1
                LEFT JOIN m_department t2 on t2.id=t1.fk_department
            ) t4 on t4.designation_id=t1.fk_designation
            LEFT JOIN m_study t5 on t5.id=t1.fk_study
            LEFT JOIN m_marital_status t6 on t6.id=t1.fk_marital_status
            LEFT JOIN m_religion t7 on t7.id=t1.fk_religion
            LEFT JOIN m_ptkp t8 on t8.id=t1.fk_ptkp
            LEFT JOIN m_users t9 on t9.fk_employee=t1.id"
        );
        return $data;
    }
}
