<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class AccountingAccount extends Model
{
    use LogsActivity;

    protected $table = 'm_accounting_accounts';

    protected $guarded = ['id'];
}
