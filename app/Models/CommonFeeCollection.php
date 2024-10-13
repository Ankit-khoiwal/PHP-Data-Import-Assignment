<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonFeeCollection extends Model
{
    use HasFactory;

    protected $fillable = ['display_receipt_id', 'due_amount', 'paid_amount'];
}
