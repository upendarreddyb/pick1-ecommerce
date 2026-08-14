<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactMessageModel extends Model
{
    protected $table = 'contact_messages';
    protected $allowedFields = ['name', 'email', 'message', 'status'];
    protected $useTimestamps = true;
}
