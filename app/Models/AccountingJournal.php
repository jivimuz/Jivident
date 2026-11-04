<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class AccountingJournal extends Model
{
    use LogsActivity;

    protected $table = 't_accounting_journals';

    protected $guarded = ['id'];

    protected $casts = ['journal_date' => 'date:Y-m-d'];

    public function lines()
    {
        return $this->hasMany(AccountingJournalLine::class, 'journal_id');
    }
}
