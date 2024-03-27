<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketModel extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'price', 'quantity'];

    protected $table="tickets";
    protected $primaryKey="id";
}
