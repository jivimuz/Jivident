<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingJournalLine extends Model
{
    protected $table = 't_accounting_journal_lines';

    protected $guarded = ['id'];

    public function account()
    {
        return $this->belongsTo(AccountingAccount::class, 'account_id');
    }
}
