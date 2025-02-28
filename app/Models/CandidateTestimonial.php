<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CandidateTestimonial extends Model
{
    use HasFactory ;

    protected $table = 'candidate_testimonials';
    protected $fillable = ['name', 'designation', 'feedback', 'photo_url', 'review_rating', 'uuid'];

    /**
     * Overriding primary key column
     */
    
}