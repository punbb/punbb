<?php
/**
 * Loader class for inject CSS and JS files.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on Drupal code
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


// JS groups
define('FORUM_JS_GROUP_SYSTEM', -100);
define('FORUM_JS_GROUP_DEFAULT', 0);
define('FORUM_JS_GROUP_COUNTER', 100);

// CSS groups
define('FORUM_CSS_GROUP_SYSTEM', -100);
define('FORUM_CSS_GROUP_DEFAULT', 0);


class Loader
{
	private $libs;

	// Class instance
	private static $instance;


	// Start of life
	private function __construct() {
		$this->libs = array();
		$this->libs['js'] = array();
		$this->libs['css'] = array();
	}


	// The end
	public function __destruct() {
		unset($this->libs);
	}


	// Singleton
	public static function singleton() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}

		return self::$instance;
	}


	// Clone forbiden
	public function __clone() {
		trigger_error('Clone is forbiden.', E_USER_ERROR);
	}


	// Add JS url to load
	public function add_js($data = NULL, $options = NULL)
	{
		$return = ($hook = get_hook('ld_fn_add_js_start')) ? eval($hook) : null;
		if ($return != null)
			return $return;

		if (is_null($options) || !is_array($options))
		{
			$options = array();
		}

		// Default options
		$default_options = array(
			// url, inline
			'type'		=> array(
				'default'	=> 'url',
			),

			//
			'async'			=> array(
				'default'	=> false,
			),

			//
			'weight'		=> array(
				'default'	=> 100,
			),

			//
			'group'			=> array(
				'default'	=> FORUM_JS_GROUP_DEFAULT,
			),

			//
			'every_page'	=> array(
				'default'	=> false,
			),

			//
			'defer'			=> array(
				'default'	=> false,
			),

			//
			'preprocess'	=> array(
				'default'	=> true,
			)

		);

		$length = count($default_options);
		$keys = array_keys($default_options);

		for ($i = 0; $i < $length; $i++)
		{
			$key = $keys[$i];

			if (!isset($options[$key]))
			{
				$default_options[$keys[$i]] = $default_options[$keys[$i]]['default'];
				continue;
			}

			$default_options[$keys[$i]] = $options[$key];
		}

		// Check data — url or inline code
		$default_options['data'] = forum_trim($data);
		if (empty($default_options['data']) || utf8_strlen($default_options['data']) < 1)
		{
			return FALSE;
		}

		// Tweak weight
		$default_options['weight'] += count($this->libs['js']) / 1000;

		($hook = get_hook('ld_fn_add_js_pre_merge')) ? eval($hook) : null;

		// Add to libs
		if ($default_options['type'] != 'inline')
		{
			$this->libs['js'][$default_options['data']] = $default_options;
		}
		else
		{
			$this->libs['js'][] = $default_options;
		}

		($hook = get_hook('ld_fn_add_js_end')) ? eval($hook) : null;

		return $this->libs['js'];
	}


	//
	public function render_js()
	{
		$output = '';

		$return = ($hook = get_hook('ld_fn_render_js_start')) ? eval($hook) : null;
		if ($return != null)
			return $return;

		if (empty($this->libs['js']))
			return $output;

		// Sorts the scripts into correct order
		uasort($this->libs['js'], array('Loader', 'sort_libs'));

		if (defined('FORUM_DISABLE_ASYNC_JS_LOADER'))
		{
			return $this->render_js_simple();
		}

		return $this->render_js_labjs();
	}


	// Add CSS url to load
	public function add_css($data = NULL, $options = NULL)
	{
		$return = ($hook = get_hook('ld_fn_add_css_start')) ? eval($hook) : null;
		if ($return != null)
			return $return;

		if (is_null($options) || !is_array($options))
		{
			$options = array();
		}

		// Default options
		$default_options = array(
			//
			'type'		=> array(
				'default'	=> 'url',
			),

			//
			'weight'		=> array(
				'default'	=> 100,
			),

			//
			'group'			=> array(
				'default'	=> FORUM_CSS_GROUP_DEFAULT,
			),

			// screen, all, print
			'media'			=> array(
				'default'	=> 'all',
			),

			//
			'every_page'	=> array(
				'default'	=> false,
			),

			//
			'preprocess'	=> array(
				'default'	=> true,
			),

			'browsers'		=> array(
				'default'	=> array(),
			),

			//
			'noscript'		=> array(
				'default'	=> false,
			)
		);

		$length = count($default_options);
		$keys = array_keys($default_options);

		for ($i = 0; $i < $length; $i++)
		{
			$key = $keys[$i];

			if (!isset($options[$key]))
			{
				$default_options[$keys[$i]] = $default_options[$keys[$i]]['default'];
				continue;
			}

			$default_options[$keys[$i]] = $options[$key];
		}

		// Check data — url or inline code
		$default_options['data'] = forum_trim($data);
		if (empty($default_options['data']) || utf8_strlen($default_options['data']) < 1)
		{
			return FALSE;
		}

		// Tweak weight
		$default_options['weight'] += count($this->libs['css']) / 1000;

		($hook = get_hook('ld_fn_add_css_pre_merge')) ? eval($hook) : null;

		// Add to libs
		if ($default_options['type'] != 'inline')
		{
			$this->libs['css'][$default_options['data']] = $default_options;
		}
		else
		{
			$this->libs['css'][] = $default_options;
		}

		($hook = get_hook('ld_fn_add_css_end')) ? eval($hook) : null;

		return $this->libs['css'];
	}


	// Render CSS libs
	public function render_css()
	{
		$output = '';

		$return = ($hook = get_hook('ld_fn_render_css_start')) ? eval($hook) : null;
		if ($return != null)
			return $return;

		if (empty($this->libs['css']))
			return $output;

		// Sorts the scripts into correct order
		uasort($this->libs['css'], array('Loader', 'sort_libs'));

		return $this->render_css_simple();
	}


	// Render for CSS — use link tags method
	private function render_css_simple()
	{
		$output = '';
		$libs = $this->libs['css'];

		$return = ($hook = get_hook('ld_fn_render_css_simple_start')) ? eval($hook) : null;
		if ($return != null)
			return $return;


		foreach ($libs as $key => $lib)
		{
			if ($lib['type'] == 'inline')
			{
				if ($lib['noscript'] === true)
					$output .= forum_trim($this->check_conditional_comments($lib, '<noscript><style>'.$lib['data'].'</style></noscript>'))."\n";
				else
					$output .= forum_trim($this->check_conditional_comments($lib, '<style>'.$lib['data'].'</style>'))."\n";
				unset($libs[$key]);
				continue;
			}
			else if ($lib['type'] == 'url')
			{
				if ($lib['noscript'] === true)
					$output .= forum_trim($this->check_conditional_comments($lib, '<noscript><link rel="stylesheet" type="text/css" media="'.$lib['media'].'" href="'.$lib['data'].'" /></noscript>'))."\n";
				else
					$output .= forum_trim($this->check_conditional_comments($lib, '<link rel="stylesheet" type="text/css" media="'.$lib['media'].'" href="'.$lib['data'].'" />'))."\n";
				unset($libs[$key]);
				continue;
			}
		}

		($hook = get_hook('ld_fn_render_css_simple_end')) ? eval($hook) : null;

		return $output;
	}



	// Render for JS — use default script tags method
	private function render_js_simple()
	{
		$output = '';
		$libs = $this->libs['js'];

		$return = ($hook = get_hook('ld_fn_render_js_simple_start')) ? eval($hook) : null;
		if ($return != null)
			return $return;

		foreach ($libs as $key => $lib)
		{
			if ($lib['type'] == 'inline')
			{
				$output .= '<script>'.$lib['data'].'</script>'."\n";
				unset($libs[$key]);
				continue;
			}
			else if ($lib['type'] == 'url')
			{
				$output .= '<script'.(($lib['async']) ? " async" : "").(($lib['defer']) ? " defer=\"true\"" : "").' src="'.$lib['data'].'"></script>'."\n";
				unset($libs[$key]);
				continue;
			}
		}

		($hook = get_hook('ld_fn_render_js_simple_end')) ? eval($hook) : null;

		return $output;
	}


	// Render for JS — use LABjs method
	private function render_js_labjs()
	{
		$output_system = $output_counter = $output_default = '';
		$libs = $this->libs['js'];

		$return = ($hook = get_hook('ld_fn_render_js_labjs_start')) ? eval($hook) : null;
		if ($return != null)
			return $return;


		foreach ($libs as $key => $lib)
		{
			if ($lib['data'] === FALSE)
			{
				continue;
			}

			if ($lib['type'] == 'inline')
			{
				if ($lib['group'] == FORUM_JS_GROUP_SYSTEM)
				{
					$output_system .= '<script>'.$lib['data'].'</script>'."\n";
				}
				else if ($lib['group'] == FORUM_JS_GROUP_COUNTER)
				{
					$output_counter .= '<script>'.$lib['data'].'</script>'."\n";
				}
				else
				{
					$output_default .= "\n\t".'.wait(function () { '.$lib['data'].' })';
				}

				unset($libs[$key]);
				continue;
			}
			else if ($lib['type'] == 'url')
			{
				if ($lib['group'] == FORUM_JS_GROUP_SYSTEM)
				{
					$output_system .= '<script src="'.$lib['data'].'"'.(($lib['async']) ? " async" : "").(($lib['defer']) ? " defer=\"true\"" : "").'></script>'."\n";
				}
				else if ($lib['group'] == FORUM_JS_GROUP_COUNTER)
				{
					$output_counter .= '<script src="'.$lib['data'].'"'.(($lib['async']) ? " async" : "").(($lib['defer']) ? " defer=\"true\"" : "").'></script>'."\n";
				}
				else
				{
					$output_default .= "\n\t".'.script("'.$lib['data'].'")'.(($lib['async']) ? "" : ".wait()");
				}

				unset($libs[$key]);
				continue;
			}
		}

		// Wrap default to LABjs parameters
		if ($output_default != '')
		{
			$output_default = '<script>'."\n\t".'$LAB.setOptions({AlwaysPreserveOrder:false})'.$output_default.';'."\n".'</script>';
		}

		($hook = get_hook('ld_fn_render_js_labjs_end')) ? eval($hook) : null;

		return $output_system.$output_default.$output_counter;
	}


	// Sort libs
	private static function sort_libs($a, $b)
	{
		$return = ($hook = get_hook('ld_fn_sort_libs_start')) ? eval($hook) : null;
		if ($return != null)
			return $return;


		// 1. Sort by group — system first
		if ($a['group'] < $b['group'])
		{
			return -1;
		}
		elseif ($a['group'] > $b['group'])
		{
			return 1;
		}

		// 2. Within a group, order all infrequently needed, page-specific files after
		// common files needed throughout the website. Separating this way allows for
		// the aggregate file generated for all of the common files to be reused
		// across a site visit without being cut by a page using a less common file.
		elseif ($a['every_page'] && !$b['every_page'])
		{
			return -1;
		}
		elseif (!$a['every_page'] && $b['every_page'])
		{
			return 1;
		}

		// 3. Sort by weight
		elseif ($a['weight'] < $b['weight'])
		{
			return -1;
		}
		elseif ($a['weight'] > $b['weight'])
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}


	// Helper func for render_* — wrap lib in IE-conditional comments
	private function check_conditional_comments($element, $data)
	{
		$return = ($hook = get_hook('ld_fn_check_conditional_comments_start')) ? eval($hook) : null;
		if ($return != null)
			return $return;

		$browsers = (isset($element['browsers']) && is_array($element['browsers'])) ? $element['browsers'] : array();
		$browsers += array('IE' => TRUE, '!IE' => TRUE);

		// If rendering in all browsers, no need for conditional comments.
		if ($browsers['IE'] === true && $browsers['!IE'])
		{
			return $data;
		}

		// Determine the conditional comment expression for Internet Explorer to evaluate.
		if ($browsers['IE'] === TRUE)
		{
			$expression = 'IE';
		}
		elseif ($browsers['IE'] === FALSE)
		{
			$expression = '!IE';
		}
		else
		{
			$expression = $browsers['IE'];
		}

		if (!$browsers['!IE'])
		{
			// "downlevel-hidden".
			$data = "\n<!--[if $expression]>".$data."<![endif]-->";
		}
		else
		{
			// "downlevel-revealed".
			$data = "\n<!--[if $expression]><!-->".$data."<!--<![endif]-->";
		}

		return $data;
	}
}


// Create the loader adapter object
$forum_loader = Loader::singleton();

?>
