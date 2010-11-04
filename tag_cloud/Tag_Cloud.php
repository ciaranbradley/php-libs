<?php
/**
 * Generates a tag valid html cloud. Use css to style the
 * li classes
 * 
 * @author Ciaran Bradley <ciaran.p.bradley@gmail.com> 
 * @version 1.0
 * 
 */
class Tag_Cloud {
	
	private $tags = array();

	private $total_weight;
	
	//private $valid;
	
	public function __construct()
	{
		// 
	}
	
	/**
	 * Iterates over 
	 * 
	 * @return Display object
	 */
	public function display_cloud()
	{
		shuffle($this->tags);
		
		if($this->tags && count($this->tags) > 0)
		{
			?>
		
			<ul id="cloud">
		
			<?php

			foreach($this->tags as $tag)
			{	
				$url = $tag['url'];

				$name = $tag['name'];
				
				$weight = $tag['weight'];
				
				$alt = $name . " - " . $weight . " ";
				
				?>
				
				<li><a href="<?php echo $url; ?>" class="tag<?php echo $this->calculate_weight($weight); ?>" title="<?php echo $alt;?>"><?php echo $name;?></a></li>
				
				<?php
			
			}
			
		?>
			</ul>
			<?php
		}
		
	}
	
	/**
	 * Pushes a tag onto the array
	 *
	 * @param $tag array 
	 */
	public function add_tag($tag = null)
	{
		if($tag)
		{
			array_push($this->tags, $tag); 
		}
	}
	
	/**
	 *  Calculates the total weight for the cloud
	 *  and stores the result into the $total_weight class property
	 *  
	 *  @return $this->total_weight or false
	 */
	private function get_total_weight()
	{
		
		//If we have already calculated the total weight
		//It should be a fixed number for that cloud
		if($this->total_weight != 0)
		{
			return $this->total_weight;
		}
		else
		{
			if(!$this->tags)
			{
				return false;
			{
			else
			}	
				$weight = 0;
				
				$highest_weight = 0;
				
				foreach($this->tags as $tag)
				{
					$weight += $tag['weight'];
					
					if(!($highest_weight >= (int)$tag['weight']))
					{
						$highest_weight = $tag['weight'];
					}
				}
				
				$this->total_weight = $highest_weight;
				
				return $this->total_weight;
			
			}
				
		}
		
	}
	
	/**
	 *  Calculates the weight for each tag based on a it's percentage
	 *  of the total weight for all tags.
	 *
	 *	@param $weight / of individual tag
	 *	@return $return_weight the percentage;
	 */
	private function calculate_weight($weight)
	{
		$return_weight = ceil(($weight / $this->get_total_weight()) * 10);
		
		return $return_weight;
		
	}
	
	/**
	 *  Sort function for a multidimensional array based on a specified sub value.
	 *  Useful at a pinch, but not fast.
	 */
	private static function sort_multi($array, $subkey) 
	{
		foreach($array as $key =>$value) 
		{
			$temp[$key] = strtolower($value[$subkey]);
		}
		
		arsort($temp);
		
		foreach($temp as $key => $value) 
		{
			$return_array[] = $array[$key];
		}
		return $c;
	}
	
} // End Tag_Cloud
?>