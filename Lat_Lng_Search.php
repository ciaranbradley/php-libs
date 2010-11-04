<?php 
/**
 * Library class Lat_Lng_Search
 *  
 * Leverages Googlemap API to pull back a (WGS84?) lat long pair.  Requires CURL 
 * 
 * @author Ciaran Bradley
 * 
 * @usage $lat_lng_array = Lat_Lng_Search::lookup_address("1 The North Pole, North Pole");
 */
class Lat_Lng_Search
{
	/**
	 * Performs the search using google api
	 * 
	 * @param $address string
	 * @return $result or false (result is array('lat' => float, 'lng' => float) 
	 */
	public static function lookup_address($address)
	{
		
		$url = 'http://maps.google.com/maps/api/geocode/xml?address=' . urlencode($address) . '&sensor=true';
		
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
		
		$xml = new SimpleXMLElement($response);

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
				
				return $result;
			}
		}
		
		return false; // If you get here, it's all gone wrong
	}
	
}// End Lat_Lng_Search
?>