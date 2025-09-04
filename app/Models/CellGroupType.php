<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CellGroupType extends Model
{
    protected $table = 'cell_group_types';
    protected $fillable = ['name'];

    public function cellGroups()
    {
        return $this->hasMany(CellGroup::class, 'cell_group_type_id');
    }
}
