<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialTrans extends Model
{
    use HasFactory;

    protected $fillable = ['voucher_no', 'amount', 'paid_amount', 'entry_mode'];
}
