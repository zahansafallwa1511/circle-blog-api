<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Article extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'articles';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The number of items to display per page for the model.
     *
     * @var string
     */
    protected $perPage = 30;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * The mass-assignable attributes for the model.
     *
     * @var Array
     */
    protected $fillable = [
        'title',
        'description',
        'author_id'
    ];

    /**
     * Define the relationship with the User model.
     *
     * @return Eloquent   Model with attached relationship.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }
}
