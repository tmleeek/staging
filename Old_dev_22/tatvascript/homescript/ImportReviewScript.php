<?php

define('MAGENTOROOT', dirname(__FILE__));
require_once(MAGENTOROOT.'/app/Mage.php');

$exporter = new ReviewList();

class ReviewList
{

	// Initialize the Mage application
	function __construct()
	{
	
		ini_set('max_execution_time', "-1");
		ini_set('memory_limit', "-1");
		ini_set('auto_detect_line_endings',TRUE);
		chdir(MAGENTOROOT);
		umask(0);
		
		// Initialize the admin application
		Mage::app('admin');
		
		
		$orderId = 67607;
		$order = Mage::getModel('sales/order')->load($orderId);
		$address = $order->getShippingAddress();
		
		$shipmentId = 32785;
		$shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
		
		echo '<pre>';
		var_dump($address->getData());
		exit;
		
		$shipment->setPackages('colissimo');
		$response = Mage::getModel('shipping/shipping')->requestToShipment($shipment);
		echo '<pre>';
		var_dump($response->getData());
		exit;
		
		//$storeId = Mage::app()->getStore()->getStoreId();
		
		//Mage::getModel('recertification/notifications')->inActiveUsers();
		//Mage::getModel('recertification/notifications')->expiredUsers();
		//Mage::getModel('recertification/notifications')->upcomingExpiryUsers();
		
		Mage::getModel('recertification/notifications')->firstReminderEmail();
		//Mage::getModel('recertification/notifications')->secondReminderEmail();
		//Mage::getModel('recertification/notifications')->thirdReminderEmail();
		
		exit;
		
		$resultData = array();	
		$response = $this->getResponse();
		echo '<pre>';
		//var_dump($response);
		//exit;
		
		$coord = $response->coord;
		$weatherInfos = $response->weather;
		$main = $response->main;
		$wind = $response->wind;
		$sys = $response->sys;

		$resultData['city_id'] = $response->id;  			// City ID
		$resultData['city_name'] = $response->name;			// City Name
		$resultData['cod'] = $response->cod;				// Internal parameter
		$resultData['visibility'] = $response->visibility; 	// Visibility, meter
		
		$resultData['latitude'] = $coord->lat;				// City geo location, latitude
		$resultData['longitude'] = $coord->lat;				// City geo location, longitude
		
		foreach($weatherInfos as $weatherInfo) {
			$resultData['weather_title'] = $weatherInfo->main;			// Group of weather parameters (Rain, Snow, Extreme etc.)
			$resultData['weather_desc'] = $weatherInfo->description;	// Weather condition within the group
			$resultData['weather_icon'] = $weatherInfo->icon;			// Weather icon id
			break;
		}
		
		$resultData['temp'] = $main->temp;					// Temperature. Unit Default: Kelvin, Metric: Celsius, Imperial: Fahrenheit.
		$resultData['temp_min'] = $main->temp_min;			// Minimum temperature at the moment.
		$resultData['temp_max'] = $main->temp_max;			// Maximum temperature at the moment.
		$resultData['pressure'] = $main->pressure;			// Atmospheric pressure hPa
		$resultData['humidity'] = $main->humidity;			// Humidity, %
		
		$resultData['wind_speed'] = $wind->speed;			// Wind speed. Unit Default: meter/sec,
		$resultData['wind_deg'] = $wind->deg;				// Wind direction, degrees (meteorological)
		
		$resultData['country'] = $sys->country;				// Country code (GB, JP etc.)
		$resultData['sunrise'] = $sys->sunrise;				// Sunrise time, unix, UTC
		$resultData['sunset'] = $sys->sunset;				// Sunset time, unix, UTC
		
		var_dump($resultData);
		
		exit;
		
		exit;
		echo 'Update Script File To import Reviews';
		exit;


	}
	
	
	public function getResponse(){
	
		$string = '{"coord":{"lon":-0.13,"lat":51.51},"weather":[{"id":800,"main":"Clear","description":"clear sky","icon":"01d"}],"base":"stations","main":{"temp":277.6,"pressure":1029,"humidity":75,"temp_min":277.15,"temp_max":278.15},"visibility":10000,"wind":{"speed":4.1,"deg":350},"clouds":{"all":0},"dt":1483361400,"sys":{"type":1,"id":5091,"message":0.0066,"country":"GB","sunrise":1483344355,"sunset":1483373047},"id":2643743,"name":"London","cod":200}';
		
		$response = json_decode($string);
		return $response;
		
	}	
	
	
	public function getProductReviews($productId){
		
		$reviews = Mage::getModel('review/review')
						->getResourceCollection()
						//->addStoreFilter(Mage::app()->getStore()->getId())
						->addEntityFilter('product', $productId)
						->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
						->setDateOrder()
						->addRateVotes();

		$avg = 0;
		$reviewsData = array();
		if (count($reviews) > 0) {
			foreach ($reviews->getItems() as $review) {
				$newReview = array();
				$newReview['nickname'] 		= $review->getNickname();
				$newReview['detail'] 		= $review->getDetail();
				$newReview['title'] 		= $review->getTitle();
				$newReview['customer_id'] 	= $review->getCustomerId();
				$newReview['status_id'] 	= $review->getStatusId();
				$newReview['created_at'] 	= $review->getCreatedAt();
				
				$votes = array();
				foreach($review->getRatingVotes() as $vote) {
					$newVote = array();
					$newVote['customer_id'] = $vote->getCustomerId();
					$newVote['percent'] 	= $vote->getPercent();
					$newVote['value'] 		= $vote->getValue();
					$newVote['rating_code'] = $vote->getRatingCode();
					$newVote['store_id'] 	= $vote->getStoreId();
					$newVote['option_id'] 	= $vote->getOptionId();
					$newVote['rating_id'] 	= $vote->getRatingId();
					$votes[] = $newVote;
				}
				$newReview['rating_votes'] 	= $votes;
				$reviewsData[] = $newReview;
			}
		}
		return $reviewsData;
	}
	
	

}

?>
  