<?php
return array(
    // set your paypal credential  //test
    'client_id' => env('PAYPAL_ID'),
    'secret' => env('PAYPAL_SECRET'),
 
	// Live Credentials
    // 'client_id' => 'ARBGzkJvTjAPR8yfjb2Elp-CIXFx9GtkJ9OE7McSq5P8O75Q-PsR2hEKYI4xAZDv79UdzMbqG3eEdhZG',
    // 'secret' => 'EN1rmRdkQ6UgFmwbIRUMcyJ0GRn03pcpblelhnSSwHLNU9g0USEKwLfl0DzS9TVPZm7_9CzZcnLbME4t',
 
    /**
     * SDK configuration
     */
    'settings' => array(
        /**
         * Available option 'sandbox' or 'live'
         */
        // 'mode' => 'live',
		'mode' => env('PAYPAL_MODE'),
        /**
         * Specify the max request time in seconds
         */
        'http.ConnectionTimeOut' => 30,
 
        /**
         * Whether want to log to a file
         */
        'log.LogEnabled' => true,
 
        /**
         * Specify the file that want to write on
         */
        'log.FileName' => storage_path() . '/logs/paypal.log',
 
        /**
         * Available option 'FINE', 'INFO', 'WARN' or 'ERROR'
         *
         * Logging is most verbose in the 'FINE' level and decreases as you
         * proceed towards ERROR
         */
        'log.LogLevel' => 'FINE'
    ),
);
