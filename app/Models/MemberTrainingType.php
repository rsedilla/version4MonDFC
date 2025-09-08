<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberTrainingType extends Model
{
    protected $table = 'member_training_type';
    
    protected $fillable = [
        'member_id',
        'training_type_id',
        'training_status_id',
    ];

    /**
     * Boot the model to add validation rules
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            // Additional validation before saving
            $exists = static::where('member_id', $model->member_id)
                ->where('training_type_id', $model->training_type_id)
                ->when($model->exists, function ($query) use ($model) {
                    return $query->where('id', '!=', $model->id);
                })
                ->exists();
                
            if ($exists) {
                throw new \Exception('This member is already assigned to this training type.');
            }
        });
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function trainingType(): BelongsTo
    {
        return $this->belongsTo(TrainingType::class);
    }

    public function trainingStatus(): BelongsTo
    {
        return $this->belongsTo(TrainingStatus::class);
    }
}
