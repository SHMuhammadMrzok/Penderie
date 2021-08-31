<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Language Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Language
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/language.html
 */
class CI_Lang {

	var $CI;
	var $idiom;
	var $lang_id;
    var $set;
	var $line;
	
	/**
	 * List of translations
	 *
	 * @var array
	 */
	var $language	= array();
	/**
	 * List of loaded language files
	 *
	 * @var array
	 */
	var $is_loaded	= array();

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
        //parent::__construct();
		log_message('debug', "Language Class Initialized");
	}
    
	// --------------------------------------------------------------------

	/**
	 * Load a language file
	 *
	 * @access	public
	 * @param	mixed	the name of the language file to be loaded. Can be an array
	 * @param	string	the language (english, etc.)
	 * @param	bool	return loaded array of translations
	 * @param 	bool	add suffix to $langfile
	 * @param 	string	alternative path to look for language file
	 * @return	mixed
	 */
	function load($langfile = '', $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '')
	{
	    $this->CI = &get_instance();
		
		$this->set = $langfile;
        $this->idiom = $idiom;
		
		$langfile = str_replace('.php', '', $langfile);

		if ($add_suffix == TRUE)
		{
			$langfile = str_replace('_lang.', '', $langfile).'_lang';
		}

		$langfile .= '.php';

		if (in_array($langfile, $this->is_loaded, TRUE))
		{
			return;
		}

		$config =& get_config();

		if ($idiom == '')
		{
			$deft_lang = ( ! isset($config['language'])) ? 'english' : $config['language'];
			$idiom = ($deft_lang == '') ? 'english' : $deft_lang;
			
			$this->idiom = $idiom;
		}

		// Determine where the language file is and load it
		if ($alt_path != '' && file_exists($alt_path.'language/'.$idiom.'/'.$langfile))
		{
			include($alt_path.'language/'.$idiom.'/'.$langfile);
		}
		else
		{
			$found = FALSE;

			foreach (get_instance()->load->get_package_paths(TRUE) as $package_path)
			{
				if (file_exists($package_path.'language/'.$idiom.'/'.$langfile))
				{
					include($package_path.'language/'.$idiom.'/'.$langfile);
					$found = TRUE;
					break;
				}
			}

			if ($found !== TRUE)
			{
				$this->lang_id = $this->_get_lang_id();
				
                $database_lang =  $this->_get_from_db();
                if ( ! empty( $database_lang ) )
                {
                    $lang = $database_lang;
                }else{
                    show_error('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
                }
			}
		}


		if ( ! isset($lang))
		{
			log_message('error', 'Language file contains no data: language/'.$idiom.'/'.$langfile);
			return;
		}

		if ($return == TRUE)
		{
			return $lang;
		}

		$this->is_loaded[] = $langfile;
		$this->language = array_merge($this->language, $lang);
		unset($lang);

		log_message('debug', 'Language file loaded: language/'.$idiom.'/'.$langfile);
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a single line of text from the language array
	 *
	 * @access	public
	 * @param	string	$line	the language line
	 * @return	string
	 */
	function line($line = '')
	{
	   	$value = ($line == '' OR ! isset($this->language[$line])) ? FALSE : $this->language[$line];

		// Because killer robots like unicorns!
		if ($value === FALSE)
		{
			log_message('error', 'Could not find the language line "'.$line.'"');
		}

		return $value;
	}
	
	/**
     * Load a language from database
     *
     * @access    private
     * @return    array
     */
    private function _get_from_db2()
    {
        $CI =& get_instance();

        $CI->db->select   ('*');
        $CI->db->from     ('lang_words');
        $CI->db->where    ('lang_id', $this->lang_id);
        //$CI->db->where    ('lang_var', $this->set);

        $query = $CI->db->get()->result();

        foreach ( $query as $row )
        {
            $return[$row->lang_var] = $row->lang_text;
        }

        unset($CI, $query);
        return $return;
    }
    
    private function _get_from_db()
    {
        $CI =& get_instance();
        
        $CI->db->select('lang_vars.lang_var, lang_translation.lang_definition');
        $CI->db->join('lang_translation', 'lang_vars.id=lang_translation.var_id');
        $CI->db->where('lang_translation.lang_id', $this->lang_id);

        $query = $CI->db->get('lang_vars');
        
        if($query)
        {
            foreach ( $query->result() as $row )
            {
                $return[$row->lang_var] = $row->lang_definition;
            }
        }

        unset($CI, $query);
        return $return;
    }
	
	/**
     * Load a language id from database
     *
     * @access    private
     * @return    integer
     */
    private function _get_lang_id()
    {
        $CI =& get_instance();

        $CI->db->select   ('*');
        $CI->db->from     ('languages');
        $CI->db->where    ('language', $this->idiom);

        $row = $CI->db->get()->row();

        unset($CI);
        return $row->id;
    }

}
// END Language Class

/* End of file Lang.php */
/* Location: ./system/core/Lang.php */
