<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $table = "blogs";
    protected $fillable = ['title', 'image', 'description', 'creator_name', 'creator_image', 'estimated_reading_time', 'total_favorites','valid_user'];
}
