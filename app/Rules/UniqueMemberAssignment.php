<?php

namespace App\Rules;

use App\Services\MemberDuplicationService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

class UniqueMemberAssignment implements ValidationRule
{
    protected string $targetTable;
    protected ?Model $currentRecord;

    public function __construct(string $targetTable, ?Model $currentRecord = null)
    {
        $this->targetTable = $targetTable;
        $this->currentRecord = $currentRecord;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $memberId = (int) $value;
        
        // Check if member can be assigned to this table
        if (!MemberDuplicationService::canAssignToTable($memberId, $this->targetTable, $this->currentRecord)) {
            $assignments = MemberDuplicationService::isAlreadyAssigned($memberId, $this->currentRecord);
            $errorMessage = MemberDuplicationService::getDuplicationErrorMessage($assignments);
            
            $fail("Cannot assign this member: {$errorMessage}");
        }
    }
}
