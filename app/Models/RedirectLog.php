<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedirectLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'redirect_id',
        'ip',
        'user_agent',
        'referer',
        'query_params',
        'access_time',
    ];

    // Relacionamento com o modelo Redirect
    public function redirect()
    {
        return $this->belongsTo(Redirect::class);
    }
}