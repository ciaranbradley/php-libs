<?php 
/***
 * 
 * 	Quick/Dirty abstraction of the google lat long search
 *  
 * 
 */

class Lat_Lng_Search
{
	
	public static function lookup_address($address)
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
	
}

?>