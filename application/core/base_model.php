<?php
/**
* Custom abstract model that extends the core CodeIgniter model class
* and that all models will extend. Define some functions to help generate 
* the queries.
*
* @package PeopleFund
* @category Administration
* @author MTR Design
* @link http://peoplefund.it
*/
abstract class Base_Model extends CI_Model {

	/**
	* Inherit the parent constructior
	*
	* @access public
	*/
	public function __construct() 
	{
		parent::__construct();
	}

	/**
	* Generate where string from array which is usually passed from the controllers to the models. 
	* The method can generate where string whith operators such as OR and LIKE and can exclude elements 
	* from the query. To be used in the sql queries.
	*
	* @param array $array Array from which where string will be generated
	* @access public
	*/
    public function where_string_from_array($array = "") 
    {
        // If we don't have where conditions
        if ( ! is_array($array) OR empty($array))
            return;

        // Loop the where array
        foreach ($array AS $k => $v) 
        {		
			// If excluding elements
			if ($k == "exclude")
			{				
				foreach ($v['values'] AS $value)
				{
					$where[] = $v['field']." != '".mysql_real_escape_string($value)."'";
				}
				$input[] = "(".implode(" AND ", $where).")";				
            }
            // If searching for elements AND "OR" is used        
            else if (strstr($v, "%") && strstr($v, " OR ")) 
            {
				$v = explode('\'', $v);
				for($i = 0; $i < count($v); $i++) {
					if(!isset($v[($i+2)])) break;
					
					$values[] = $v[($i+1)]." '" . mysql_real_escape_string($v[($i+2)]) . "'";
					$i++;
				}
                $input[] = "(".$k . " LIKE '" . mysql_real_escape_string($v[0]) . "' ".join(' ', $values).")";
			}
            // If searching for elements            
            else if (strstr($v, "%")) 
            {
                $input[] = "(".$k . " LIKE '" . mysql_real_escape_string($v) . "')";
			}
			// If OR is used
			else if (strstr($v, " OR ")) 
			{
                $input[] = "(".$k . " = '" . mysql_real_escape_string($v) . "')";
			} 
			// Not equal
			else if (strstr($v, " != "))
			{
				$v = str_replace(" != ", '', mysql_real_escape_string($v));
				$input[] = $k . " != '" . mysql_real_escape_string($v) . "'";
            }
            else 
            {
                $input[] = $k . " = '" . mysql_real_escape_string($v) . "'";
            }
        }

        // Form where string
        $where = " WHERE " . implode(" AND ", $input);
		
        // Return where string
        return $where;
    }
	
	/**
	* Generate limit string from search array which is usually passed from the
	* controllers to the models. To be used in the sql queries.
	*
	* @param array $array Array from which limit string will be generated
	* @access public
	*/
	public function generate_limit($array)
	{		
		// If we don't have limit
		if (empty($array) || !isset($array['from']) || !isset($array['count']))
		{
			return;
		}
		// Generate limit
		else 
		{
			return " LIMIT ".(int) $array['from'].",".(int) $array['count'];
		}
	}
	
	/**
	* Generate order string from search array which is usually passed from the
	* controllers to the models. To be used in the sql queries.
	*
	* @param array $array Array from which order string will be generated
	* @access public
	*/ 
	public function generate_order($array)
	{		
		// If we don't have limits
		if (empty($array))
		{
			return;
		} 
		// Generate order
		else 
		{
			return " ORDER BY ".$array['by']." ".$array['type'];
		}
	}
}

/* End of file base_model.php */
/* Location: ./application/core/base_model.php */