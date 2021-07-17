<?php
/**
 * Parser Class
 *
 */
class Baseparser {
	/**
	 * Left delimiter character for pseudo vars
	 *
	 * @var string
	 */
	public $l_delim = '{';
	/**
	 * Right delimiter character for pseudo vars
	 *
	 * @var string
	 */
	public $r_delim = '}';

	// --------------------------------------------------------------------
	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
	}
	// --------------------------------------------------------------------
	/**
	 * Parse a template
	 *
	 * Parses pseudo-variables contained in the specified template view,
	 * replacing them with the data in the second param
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function parse($template_data, $data, $return = TRUE)
	{
		// ob_start();
		// require(DIR_INCLUDE.'template/receipt_template/'.$template.'.php');
		// $template_data = ob_get_contents();
		// ob_end_clean();

		return $this->_parse($template_data, $data, $return);
	}
	// --------------------------------------------------------------------
	/**
	 * Parse a String
	 *
	 * Parses pseudo-variables contained in the specified string,
	 * replacing them with the data in the second param
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function parse_string($template, $data, $return = FALSE)
	{
		return $this->_parse($template, $data, $return);
	}
	// --------------------------------------------------------------------
	/**
	 * Parse a template
	 *
	 * Parses pseudo-variables contained in the specified template,
	 * replacing them with the data in the second param
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	protected function _parse($template, $data, $return = FALSE)
	{
		if ($template === '')
		{
			return FALSE;
		}
		$replace = array();
		foreach ($data as $key => $val)
		{
			$replace = array_merge(
				$replace,
				is_array($val)
					? $this->_parse_pair($key, $val, $template)
					: $this->_parse_single($key, (string) $val, $template)
			);
		}
		unset($data);
		$template = strtr($template, $replace);
		return $template;
	}
	// --------------------------------------------------------------------
	/**
	 * Set the left/right variable delimiters
	 *
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	public function set_delimiters($l = '{', $r = '}')
	{
		$this->l_delim = $l;
		$this->r_delim = $r;
	}
	// --------------------------------------------------------------------
	/**
	 * Parse a single key/value
	 *
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _parse_single($key, $val, $string)
	{
		return array($this->l_delim.$key.$this->r_delim => (string) $val);
	}
	// --------------------------------------------------------------------
	/**
	 * Parse a tag pair
	 *
	 * Parses tag pairs: {some_tag} string... {/some_tag}
	 *
	 * @param	string
	 * @param	array
	 * @param	string
	 * @return	string
	 */
	protected function _parse_pair($variable, $data, $string)
	{
		$replace = array();
		preg_match_all(
			'#'.preg_quote($this->l_delim.$variable.$this->r_delim).'(.+?)'.preg_quote($this->l_delim.'/'.$variable.$this->r_delim).'#s',
			$string,
			$matches,
			PREG_SET_ORDER
		);
		foreach ($matches as $match)
		{
			$str = '';
			foreach ($data as $row)
			{
				$temp = array();
				foreach ($row as $key => $val)
				{
					if (is_array($val))
					{
						$pair = $this->_parse_pair($key, $val, $match[1]);
						if ( ! empty($pair))
						{
							$temp = array_merge($temp, $pair);
						}
						continue;
					}
					$temp[$this->l_delim.$key.$this->r_delim] = $val;
				}
				$str .= strtr($match[1], $temp);
			}
			$replace[$match[0]] = $str;
		}
		return $replace;
	}
}

class Parser extends Baseparser {
    function showVariable($name) {
        if (isset($this->data[$name])) {
            echo $this->data[$name];
        } else {
            echo '{' . $name . '}';
        }
    }
    function wrap($element) {
        $this->stack[] = $this->data;
        foreach ($element as $k => $v) {
            $this->data[$k] = $v;
        }
    }
    function unwrap() {
        $this->data = array_pop($this->stack);
    }
    function run() {
        ob_start ();
        eval (func_get_arg(0));
        return ob_get_clean();
    }
    function loopParse($template, $data) {
    	// $template = $this->parse($template, $data);
    	// $template = $this->ifElseParse($template, $data);
        $this->data = $data;
        $this->stack = array();

        
        $template = str_replace('<', '<?php echo \'<\'; ?>', $template);
        
        $template = preg_replace('~\{(\w+)\}~', '<?php $this->showVariable(\'$1\'); ?>', $template);
        $template = preg_replace('~\{LOOP:(\w+)\}~', '<?php foreach ($this->data[\'$1\'] as $ELEMENT): $this->wrap($ELEMENT); ?>', $template);
        $template = preg_replace('~\{ENDLOOP:(\w+)\}~', '<?php $this->unwrap(); endforeach; ?>', $template);
       
        $template = '?>' . $template;
    
        return $this->run($template);
    }

}	