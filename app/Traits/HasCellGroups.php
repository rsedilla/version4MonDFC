<?php  
  
namespace App\Traits;  
  
use App\Models\CellGroup;  
use Illuminate\Database\Eloquent\Relations\MorphMany;  
  
trait HasCellGroups  
{  
    public function cellGroups(): MorphMany  
    {  
        return $this->morphMany(CellGroup::class, 'leader');  
    }  
} 
