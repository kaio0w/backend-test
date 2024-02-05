<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Foundation\Http\FormRequest;


class Redirect extends Model
{
    
    use HasFactory, SoftDeletes;

    protected $fillable = ['url', 'status']; // Adicione os campos necessários

    // Implemente o atributo code
    public function getCodeAttribute()
    {
        return Hashids::encode($this->id);
    }
}

class CreateRedirectRequest extends FormRequest
{
    public function rules()
    {
        return [
            'url' => ['required', 'url', 'active_url', 'not_in:' . url('/')],
        ];
    }
}