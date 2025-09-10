<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Testing Direct Leader Relationship\n\n";

// Find Albert Castro
$member = App\Models\Member::where('first_name', 'Albert')
    ->where('last_name', 'Castro')
    ->first();

if (!$member) {
    echo "❌ Member not found\n";
    exit(1);
}

echo "✅ Member found: {$member->first_name} {$member->last_name}\n";
echo "Leader Type: {$member->leader_type}\n";
echo "Leader ID: {$member->leader_id}\n\n";

// Test directLeader relationship
echo "🔍 Testing directLeader relationship:\n";
try {
    $directLeader = $member->directLeader;
    if ($directLeader) {
        echo "✅ DirectLeader loaded: " . get_class($directLeader) . " (ID: {$directLeader->id})\n";
        
        if ($directLeader->member) {
            echo "✅ Leader member loaded: {$directLeader->member->first_name} {$directLeader->member->last_name}\n";
        } else {
            echo "❌ Leader member is null\n";
        }
    } else {
        echo "❌ DirectLeader is null\n";
    }
} catch (Exception $e) {
    echo "❌ Error loading directLeader: " . $e->getMessage() . "\n";
}

echo "\n🔍 Testing with eager loading:\n";
try {
    $memberWithLeader = App\Models\Member::with(['directLeader.member'])
        ->where('first_name', 'Albert')
        ->where('last_name', 'Castro')
        ->first();
    
    if ($memberWithLeader && $memberWithLeader->directLeader) {
        echo "✅ DirectLeader with eager loading: " . get_class($memberWithLeader->directLeader) . "\n";
        
        if ($memberWithLeader->directLeader->member) {
            echo "✅ Leader member with eager loading: {$memberWithLeader->directLeader->member->first_name} {$memberWithLeader->directLeader->member->last_name}\n";
        } else {
            echo "❌ Leader member is null with eager loading\n";
        }
    } else {
        echo "❌ DirectLeader is null with eager loading\n";
    }
} catch (Exception $e) {
    echo "❌ Error with eager loading: " . $e->getMessage() . "\n";
}

echo "\n🔍 Testing manual query:\n";
try {
    $seniorPastor = App\Models\SeniorPastor::with('member')->find($member->leader_id);
    if ($seniorPastor && $seniorPastor->member) {
        echo "✅ Manual query successful: {$seniorPastor->member->first_name} {$seniorPastor->member->last_name}\n";
    } else {
        echo "❌ Manual query failed\n";
    }
} catch (Exception $e) {
    echo "❌ Error with manual query: " . $e->getMessage() . "\n";
}

echo "\n✅ Relationship test completed!\n";
