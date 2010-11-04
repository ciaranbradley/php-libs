<?php
/**
 * Last FM User api
 * Contains the basics to query the last.fm api
 * 
 * Currently only the "gettopartists" and "getrecenttracks" are implemented
 * though it should be trivial to add any of the other methods through "get_method()"
 * 
 * @author Ciaran Bradley
 *
 */
class Lastfm_Search {
	
	/**
	 * The current Last.fm api url
	 * @url string
	 */
	private static $url = "http://ws.audioscrobbler.com/2.0/";
	
	/**
	 * Set your last.fm api key
	 * 
	 * @api_key string
	 */
	private static $api_key = "";
	
	/**
	 * The available user methods for last.fm
	 * 
	 * @methods array
	 */
	private static $methods = array(
										"user.getartisttracks",
										"user.getbannedtracks",
										"user.getEvents",
										"user.getfriends",
										"user.getinfo",
										"user.getlovedtracks",
										"user.getneighbours",
										"user.getnewreleases",
										"user.getpastevents",
										"user.getplaylists",
										"user.getrecentstations",
										"user.getrecenttracks",
										"user.getrecommendedartists",
										"user.getrecommendedevents",
										"user.getshouts",
										"user.gettopalbums",
										"user.gettopartists",
										"user.gettoptags",
										"user.gettoptracks",
										"user.getweeklyalbumchart",
										"user.getweeklyartistchart",
										"user.getweeklychartlist",
										"user.getweeklytrackchart",
										//"user.shout",  // I haven't looked into the documentation for this
									);
	/**
	 * User is initialised on construct
	 * 
	 * @user string
	 */
	private $user = false;
	
	/**
	 * 
	 * 
	 * @param $user a valid Last_FM user
	 */
	public function __construct($user)
	{
		$this->set_user($user);	
	}
	
	/**
	 * Gets the currently playing song on last.fm
	 * and returns it as an xml object.
	 * @return xml_object 
	 */
	public function get_recent_tracks()
	{
	
		$result = $this->get_method("user.getrecenttracks");
		
		return $result;
		
	}
	

	/**
	 *  Gets the top artists for the initialised user 
	 *	@return $result xml_object
	 */
	public function get_top_artists()
	{
		
		$result = $this->get_method("user.gettopartists");
		
		return $result;
	}
	
	/**
	 *  A general function that checks if the method is valid,
	 *  then performs the search.  Alias your required methods
	 *  using a method above.  
	 * 
	 *
	 */
	private function get_method($method = '')
	{
		if($method == '')
		{
			$return false;
		}
		
		if(!in_array($method, self::methods))
		{
			return false;
		}
		else
		{
			$params = array(
						"method" => $method,
						"user" => $this->user,
						);
						
			$search_string = $this->build_search($params);
		
			$search = $this->url.$search_string;
		
			$result = simplexml_load_file($search);
		
			return $result;
		}
		
	}
	
	/**
	 * Sets the user 
	 * 
	 * 
	 * @param $user string
	 */
	public function set_user($user)
	{
		$this->user = $user;
	}
	
	/**
	 *  Checks if the user is set
	 *  
	 *  TODO It might be nice to hit Last.fm and validate the user with them
	 *  
	 *  
	 */
	private function validate_user()
	{
		if($this->user)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * Builds the search string
	 * @param $params array
	 * @return $search_term string
	 */
	private function build_search($params)
	{
		$search_term = "";
		
		foreach($params as $key => $value)
		{
			$search_term .= "&".$key."=".$value;
		}
		
		$mark = "?";
		
		$search_term = substr($search_term, 1);
		
		$search_term = $mark.$search_term;
		
		$search_term .= "&api_key=".$self::$api_key;
		
		return $search_term;
	}
	
} // End Lastfm_Search
