<?php
/**
* This demonstrates grabbing a admin KS for a particular action ( sview ) being granted to the current user / session.
*/
$wgMwEmbedApiServices['KSTest'] = 'mweApiKSTest';

// Include the kaltura client
require_once( dirname( __FILE__ ) . '../../Client/KalturaClientHelper.php' );

class mweApiKSTest {
	function run(){
		global $wgKalturaAdminSecret;
		// validate params ( hard coded to test a particular test file / account )
		if( !isset( $_REQUEST['wid'] ) ||  $_REQUEST['wid'] != '_243342' ){
			$this->outputError( 'bad widget param');
		}
		$this->partnerId = '243342';
		if( !isset( $_REQUEST['entry_id'] ) || $_REQUEST['entry_id'] != '1_20x0ca3l' ){
			$this->outputError( 'bad entry_id param');
		}
		$this->entryId = '1_20x0ca3l';
		
		// load library and get ks for given entry:
		if( !isset( $wgKalturaAdminSecret ) || ( $wgKalturaAdminSecret == null ) ) {
			$this->outputError( 'no admin ks configured');
		}
	
		$client = $this->getClient();
		$ks = $client->session->start ( $wgKalturaAdminSecret, 
				$_SERVER['REMOTE_ADDR'], 
				KalturaSessionType::ADMIN, 
				$this->partnerId, 
				3600, // expire in one hour
				"sview:{$this->entryId}" // give permision to "view" the entry
			);
		header( 'Content-type: text/javascript');
		echo json_encode(array('ks' => $ks ) );
	}
	function getClient(){
		$conf = new KalturaConfiguration( $this->partnerId );
		$conf->serviceUrl = $this->getServiceConfig( 'ServiceUrl' );
		$conf->serviceBase = $this->getServiceConfig( 'ServiceBase' );
		return new KalturaClient( $conf );
	}
	function getServiceConfig( $name ){
		switch( $name ){
			case 'ServiceUrl' : 
				global $wgKalturaServiceUrl;
				return $wgKalturaServiceUrl;
				break;
			case 'ServiceBase':
				global $wgKalturaServiceBase;
				return $wgKalturaServiceBase;
				break;
		}
	}
	function outputError( $msg ){
		header( 'Content-type: text/javascript');
		echo json_encode(array( 'error' => $msg  ) );
		exit(1);
	}
};