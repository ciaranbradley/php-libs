<?php

class Haversine_Search 
{

	protected $lat;
	
	protected $lng;
	
	private static $miles = '3959';
	
	private static $kilometers = '6371';
	
	private $distance;
	//Sql specific
	private $limit;
	
	private $from;
	
	private $params = false;
	
	private $converter;
	
	private $table;
	
	protected $obj;
	
	public function __construct($table, $lat, $long, $param = false, $limit = 20, $from = 0, $distance = 25, $miles = true)
	{
		
		$this->lat = $lat;
		
		$this->lng = $long;
		
		$this->distance = $distance;
		
		$this->limit = $limit;
		
		$this->from = $from;
		
		$this->table = $table;
		
		$this->params = $param;
		
		if($miles == true)
		{
			$this->converter = self::miles;
		}
		else
		{
			$this->converter = self::kilometers;
		}
		
		//return $this->get_results();
		
	}

	public function get_results()
	{
	
		$distance = $this->distance;
			
		$sql = 'SELECT ID, ( 	' .$this->converter . '	* acos( 
												  cos( radians(' . $this->lat . ') ) 
												* cos( radians( Lat ) ) 
												* cos( radians( Lng ) - radians(' . $this->lng . ') ) 
												+ sin( radians(' . $this->lat . ') ) 
												* sin( radians( Lat ) ) 
											   ) 
							) 
				AS distance FROM ' .$this->table ' HAVING distance < ' . $distance ;
		
		if($this->params)
		{
			$sql .= ' AND ' . $this->params; 
		}
		
		$sql .= ' ORDER BY distance LIMIT ' . $this->from . ' , ' . $this->limit . ' ; ';
		
		$result = sql_query_multiassoc($sql);
		
		return $result;

	}
	
}
?>