<?php

namespace App\Models;

use CodeIgniter\Model;

class VideoTestimonialModel extends Model
{
    protected $table = 'video_testimonials';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'customer_name',
        'title',
        'review',
        'rating',
        'video',
        'poster',
        'external_url',
        'provider',
        'sort_order',
        'status',
    ];
}
