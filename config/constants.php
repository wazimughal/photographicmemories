<?php


return [
    'app_name'=>'Thephotographic Memories',
    'per_page'=>2,
    // Costant Value ID set for the groups table for User ROLEs
    'groups' => [
        'admin' => 1,
        'venue_group_hod' => 2,
        'customer' => 3,
        'photographer' => 4,
    ],
    'booking_status' => [
        'pencil' => 0,
        'awaiting_for_photographer' => 1,
        'declined_by_photographer' => 2,
        'pending_customer_agreement' => 3, 
        'pending_customer_deposit' => 4, 
        'on_hold' => 5, 
        'confirmed' => 5, 
    ],
    'photographer_assigned' => [
        'awaiting' => 0,
        'accepted' => 1,
        'declined' => 2,
        'cancelled' => 3, 
        'removed' => 4, 
    ],
    'pencileBy' => [
        'office' => 0,
        'venue_group' => 1,
        'website' => 2,
    ]
];

/*
    'customer_added' => 'User Added a customer',
    'new_lead_added '=> New Lead Added
    
*/
?>