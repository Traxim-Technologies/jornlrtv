<?php
return array(
    // set your paypal credential  //test
    'client_id' => 'AVPSjQbR0VdaRv2h35o6gNAFdUOM0Oe-251pPER1eX_vArda5MPI-BVXCW7uAtuMMi2OuTYsyvryexJj',
    'secret' => 'ECaFz5wYBuR2kHjDXmzQM5dIVkTYT91acDPH2v74h_J0VITI9d3IdS0BwDy6xAmV2cz6k2yiHjzMXNzH',
 
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
		'mode' => 'sandbox',
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
