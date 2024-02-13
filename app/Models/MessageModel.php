<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageModel extends Model
{
    protected $table="message_lists";
    protected $primaryKey="message_id";

    use HasFactory;
}
