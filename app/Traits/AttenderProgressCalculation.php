<?php

namespace App\Traits;

trait AttenderProgressCalculation
{
    /**
     * Calculate SUYLN progress efficiently
     */
    public function getSuylnProgressAttribute(): string
    {
        $lessons = [
            $this->suyln_lesson_1, $this->suyln_lesson_2, $this->suyln_lesson_3,
            $this->suyln_lesson_4, $this->suyln_lesson_5, $this->suyln_lesson_6,
            $this->suyln_lesson_7, $this->suyln_lesson_8, $this->suyln_lesson_9,
            $this->suyln_lesson_10
        ];
        
        $completed = count(array_filter($lessons));
        return "$completed/10";
    }

    /**
     * Calculate DCC (Sunday Service) progress efficiently
     */
    public function getDccProgressAttribute(): string
    {
        $services = [
            $this->sunday_service_1, $this->sunday_service_2,
            $this->sunday_service_3, $this->sunday_service_4
        ];
        
        $completed = count(array_filter($services));
        return "$completed/4";
    }

    /**
     * Calculate Cell Group progress efficiently
     */
    public function getCgProgressAttribute(): string
    {
        $groups = [
            $this->cell_group_1, $this->cell_group_2,
            $this->cell_group_3, $this->cell_group_4
        ];
        
        $completed = count(array_filter($groups));
        return "$completed/4";
    }

    /**
     * Get SUYLN progress color based on completion
     */
    public function getSuylnProgressColorAttribute(): string
    {
        $progress = $this->suyln_progress;
        $completed = (int)explode('/', $progress)[0];
        
        return match(true) {
            $completed === 10 => 'success',
            $completed >= 8 => 'warning',
            $completed >= 5 => 'info',
            $completed >= 1 => 'primary',
            default => 'gray'
        };
    }

    /**
     * Get DCC progress color based on completion
     */
    public function getDccProgressColorAttribute(): string
    {
        $progress = $this->dcc_progress;
        $completed = (int)explode('/', $progress)[0];
        
        return match(true) {
            $completed === 4 => 'success',
            $completed >= 3 => 'warning',
            $completed >= 2 => 'info',
            $completed >= 1 => 'primary',
            default => 'gray'
        };
    }

    /**
     * Get Cell Group progress color based on completion
     */
    public function getCgProgressColorAttribute(): string
    {
        $progress = $this->cg_progress;
        $completed = (int)explode('/', $progress)[0];
        
        return match(true) {
            $completed === 4 => 'success',
            $completed >= 3 => 'warning',
            $completed >= 2 => 'info',
            $completed >= 1 => 'primary',
            default => 'gray'
        };
    }

    /**
     * Check if SUYLN is completed
     */
    public function getIsSuylnCompletedAttribute(): bool
    {
        return $this->suyln_progress === '10/10';
    }

    /**
     * Check if DCC is completed
     */
    public function getIsDccCompletedAttribute(): bool
    {
        return $this->dcc_progress === '4/4';
    }

    /**
     * Check if Cell Group attendance is completed
     */
    public function getIsCgCompletedAttribute(): bool
    {
        return $this->cg_progress === '4/4';
    }

    /**
     * Get overall completion percentage
     */
    public function getOverallProgressAttribute(): int
    {
        $suylnCompleted = (int)explode('/', $this->suyln_progress)[0];
        $dccCompleted = (int)explode('/', $this->dcc_progress)[0];
        $cgCompleted = (int)explode('/', $this->cg_progress)[0];
        
        $totalPossible = 18; // 10 + 4 + 4
        $totalCompleted = $suylnCompleted + $dccCompleted + $cgCompleted;
        
        return round(($totalCompleted / $totalPossible) * 100);
    }
}
