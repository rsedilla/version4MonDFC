<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;

class TransferMemberLeaderType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'member:transfer {member_id} {new_leader_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer a member from one leader type to another';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $memberId = $this->argument('member_id');
        $newLeaderType = $this->argument('new_leader_type');

        // Find the member
        $member = Member::find($memberId);
        
        if (!$member) {
            $this->error("Member with ID {$memberId} not found.");
            return 1;
        }

        // Validate the leader type
        $availableTypes = Member::getAvailableLeaderTypes();
        if (!array_key_exists($newLeaderType, $availableTypes)) {
            $this->error("Invalid leader type: {$newLeaderType}");
            $this->info("Available types:");
            foreach ($availableTypes as $key => $name) {
                $this->line("  {$key} => {$name}");
            }
            return 1;
        }

        // Check if transfer is allowed
        if (!$member->canTransferTo($newLeaderType)) {
            $this->error("Cannot transfer {$member->full_name} from {$member->getLeaderTypeDisplayName()} to {$availableTypes[$newLeaderType]}");
            return 1;
        }

        $oldType = $member->getLeaderTypeDisplayName();
        $newType = $availableTypes[$newLeaderType];

        // Confirm the transfer
        if (!$this->confirm("Transfer {$member->full_name} from {$oldType} to {$newType}?")) {
            $this->info("Transfer cancelled.");
            return 0;
        }

        try {
            // Perform the transfer
            $success = $member->transferLeaderType($newLeaderType);
            
            if ($success) {
                $this->info("Successfully transferred {$member->full_name} from {$oldType} to {$newType}");
            } else {
                $this->error("Failed to transfer {$member->full_name}");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Error during transfer: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
