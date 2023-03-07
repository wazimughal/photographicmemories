<?php


return [
    'app_name'=>'Thephotographic Memories',
    'phone'=>'845-501-1888',
    'address'=>'Monsey, NY, US',
    'admin_email'=>'Joel@thephotographicmemories.com',
    'google_api_key'=>'AIzaSyA1JM99SFagfbshQ0xgQQmUXlgfvi-MUDw', // Google Api Key for Google map and place suggestion
    'date_formate'=>'m/d/Y',
    'date_and_time'=>'m/d/Y h:i:s',
    'per_page'=>10,
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
        'confirmed' => 6, 
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
        'customer' => 3,
    ]
];

/*
    'customer_added' => 'User Added a customer',
    'new_lead_added '=> New Lead Added
    
*/
?>