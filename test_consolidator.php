<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->boot();

echo "Testing ConsolidatorService...\n";

$service = app(\App\Services\ConsolidatorService::class);

echo "1. Checking SOL training types...\n";
$solTypes = \App\Models\TrainingType::whereIn('name', ['SOL 1', 'SOL 2', 'SOL 3'])->get();
echo "Found " . $solTypes->count() . " SOL training types\n";
foreach ($solTypes as $type) {
    echo "- {$type->name} (ID: {$type->id})\n";
}

echo "\n2. Checking Graduate status...\n";
$graduateStatus = \App\Models\TrainingStatus::where('name', 'Graduate')->first();
if ($graduateStatus) {
    echo "Graduate status found (ID: {$graduateStatus->id})\n";
} else {
    echo "Graduate status NOT found\n";
}

echo "\n3. Checking member training assignments...\n";
$assignments = \App\Models\MemberTrainingType::count();
echo "Total training assignments: {$assignments}\n";

echo "\n4. Testing ConsolidatorService options...\n";
try {
    $options = $service->getConsolidatorOptions();
    echo "Consolidator options count: " . count($options) . "\n";
    foreach ($options as $id => $name) {
        echo "- {$name} (ID: {$id})\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
