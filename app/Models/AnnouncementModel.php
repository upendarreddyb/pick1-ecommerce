<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table = 'announcements';
    protected $allowedFields = ['message', 'status', 'speed'];
    protected $useTimestamps = true;
}
