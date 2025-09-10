<?php

use App\Models\Member;
use Illuminate\Support\Facades\Route;

Route::get('/test-members', function () {
    $members = Member::withoutGlobalScopes()
        ->with(['directLeader.member'])
        ->orderBy('updated_at', 'desc')
        ->limit(10)
        ->get();
    
    $html = '<h1>Members Test Page</h1>';
    $html .= '<p>Generated at: ' . now() . '</p>';
    $html .= '<table border="1" style="border-collapse: collapse; width: 100%;">';
    $html .= '<tr><th>Name</th><th>Updated</th><th>Leader Type</th><th>Leader ID</th><th>Direct Leader</th></tr>';
    
    foreach ($members as $member) {
        $leaderDisplay = 'None assigned';
        if ($member->leader_type && $member->leader_id) {
            if ($member->directLeader && $member->directLeader->member) {
                $leaderDisplay = '👤 ' . $member->directLeader->member->full_name;
            } else {
                $leaderDisplay = '❌ Error loading leader';
            }
        }
        
        $style = ($member->first_name === 'Albert' && $member->last_name === 'Castro') 
            ? 'background-color: yellow; font-weight: bold;' 
            : '';
            
        $html .= "<tr style='{$style}'>";
        $html .= "<td>{$member->first_name} {$member->last_name}</td>";
        $html .= "<td>{$member->updated_at}</td>";
        $html .= "<td>{$member->leader_type}</td>";
        $html .= "<td>{$member->leader_id}</td>";
        $html .= "<td>{$leaderDisplay}</td>";
        $html .= "</tr>";
    }
    
    $html .= '</table>';
    $html .= '<p><a href="/admin">Back to Admin</a></p>';
    
    return $html;
});
