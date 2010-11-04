<?php

class Google_Map 
{
	private $api_key;

	private $center_point;

	private $zoom;
	
	private $points;

	private $width;

	private $height;

	private $directions;

	public function __construct($width = "99%", $height = "350px", $zoom="14", $api_key, $centre_point)
	{

		$this->api_key = $api_key

		$this->center_point = $centre_point;
		
		$this->points = array();
		
		$this->width = $width;

		$this->height = $height;
		
		$this->zoom = $zoom;
	}

	public function add_point($point)
	{
		$this->points[] = $point;
	}
	
	public function add_directions($directions)
	{
		$this->directions = $directions;
	}

	public function display_map()
	{
		//print_array($this->points);
		
		?>

			<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=<?=$this->api_key?>" type="text/javascript"></script>

			<script type="text/javascript">

			
				var map = null;

				var geocoder = null;

				var error = '';

				function initialize() {
				  	if(GBrowserIsCompatible()) {
						<?php
						
						if(!empty($this->points))
						{
							?>
			
					    	map = new GMap2(document.getElementById("map_canvas"));
					    	map.addControl(new GSmallMapControl());
					    	map.addControl(new GMapTypeControl());
			
							<?php
							
							//$test = empty($this->points);
							//var_dump($this->points);

							$count = 0;
							
							if($this->directions && count($this->points) == 1)
							{
								//echo $this->points[0]['address'];
								//exit();
								$this->show_directions($this->directions, $this->points[0]['address']);
							}
							else
							{
								foreach($this->points as $point)
								{	
									$lat = exist($point['lat']);
									$lng = exist($point['lng']);
									
									if(!$lat || !$lng)
									{
										$latlng = $this->lookup_address(exist($point['address']));
										
										if($latlng)
										{
											$lat = exist($latlng['lat']);
											$lng = exist($latlng['lng']);
										}
									}
					
									if($lat && $lng)
									{
										?>
					
							    	   	var point = new GLatLng(<?=$lat?>, <?=$lng?>);
	
							    	   	if(point){
								    	   	
							    	   		var marker = new GMarker(point);
								          	map.addOverlay(marker);
					
											<?php
	
											if(exist($point['details']))
											{
					 							$this->add_click_for_details($point);
											}
	
											?>
											
								        }
					
									<?php				
									
									}
									
									$count++;
								}
							}
					
							/***************************************************************/
							// SET THE CENTER POINT
							
							// if there is
							if(count($this->points) == 1)
							{
								$lat = exist($this->points[0]['lat']);
								$lng = exist($this->points[0]['lng']);
									
								if(!$lat || !$lng)
								{
									$latlng = $this->lookup_address($this->points[0]['address']);
								}
								else
								{
									$latlng = array(
									
										'lat' => $lat,
										'lng' => $lng,
									
									);	
								}								
							}
							else
							{
								$latlng = $this->lookup_address($this->center_point);
							}
						
							if($latlng)
							{
								$lat = exist($latlng['lat']);
								$lng = exist($latlng['lng']);
								
								if($lat && $lng)
								{
									?>
									
									var point = new GLatLng(<?=$lat?>, <?=$lng?>);
		
						    	   	if(point) {
						    	   		map.setCenter(point, <?=$this->zoom?>);
							        }
									
									<?php
								}
							}
							
							/***************************************************************/
						}	
							
						?>
			  		}
				}
			
			</script>

			<div id="map_canvas" style="width: <?=$this->width?>; height: <?=$this->height?>; border: 1px solid black;"></div>
			
			<?php 
			
			if($this->directions)
			{
				?>
					
				<div id="map_route" style="width: <?=$this->width?>; display: none;"></div>
					
				<?php 	
			}
			
			?>
			
			<div id="map_error" style="margin-top: 20px;"></div>

			<script type="text/javascript">

				initialize();

			</script>

		<?php
	
	}

	private function add_click_for_details($point)
	{
		$name = $point['name'];

		/**********************************************/

		$address = $point['address'];
		$address_array = explode(",", $address);
		$address = implode('<br />', $address_array);

		$url = exist($point['url']);

		if($url)
		{
			$url .= $point['id'];

			$url = "<br /><a href= '".$url."'>More Information</a>";
		}
		else
		{
			$url = "";
		}

		/**********************************************/

		$html = "<h3>$name</h3><p>$address</p><p>$url</p>";

		// there are a number of characters we need to escape

		$html = addslashes($html);

		?>

		GEvent.addListener(marker, "click", function() {marker.openInfoWindowHtml('<?=$html?>');});

		<?php
	}

	public function show_directions($directions, $address)
	{
		//var_dump($address);
		//exit();
	
		$from = $this->lookup_address($address);
		$to = $this->lookup_address($directions);
		
		if($from && $to)
		{	
			?>
									
			directionsPanel = document.getElementById("map_route");
      		directions = new GDirections(map, directionsPanel);

      		directions.load("from: <?=$from['lat']?>,<?=$from['lng']?> to: <?=$to['lat']?>,<?=$to['lng']?> ");
       		
			$('#map_route').show();

			window.location.hash = "from"; 
									
			<?php 	
		}
		else
		{
			?>
			
			error = '<p>Sorry, we could not provide directions</p>';
							
			$('#map_error').html(error);
			
			<?php 	
		}
	}

	private function lookup_address($address)
	{
		
		$url = 'http://maps.google.com/maps/api/geocode/xml?address=' . urlencode($address) . '&sensor=true';
		//echo $url;
		
		if(in_array('curl', get_loaded_extensions()))
		{
			$curl = curl_init($url);
			
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($curl);
			
			curl_close($curl);
		}
		else
		{
			$handle = fopen($url, "rb");
			
			$response = '';
			
			while(!feof($handle)) 
			{
			  $response .= fread($handle, 1024);
			}
		
			fclose($handle);
		}
		
		//print_array($response);
		//exit();
		
		$xml = new SimpleXMLElement($response);
		
		//print_array($xml);
		//exit();
		
		$status = exist($xml->status);
		
		if($status == 'OK')
		{
			$lat = (string) $xml->result->geometry->location->lat;
			$lng = (string) $xml->result->geometry->location->lng;
			
			if($lat && $lng && trim($lat) != "" && trim($lng) != "")
			{
				$result = array(
				
					'lat' => $lat,
					'lng' => $lng,
				
				);
				
				//print_array($result); 
				//exit();
				
				return $result;
			}
		}
		
		return false;
	}
	
	public static function get_instance($width="99%", $height="350px", $zoom="14")
	{
		return new Google_Map($width, $height, $zoom);
	}
}