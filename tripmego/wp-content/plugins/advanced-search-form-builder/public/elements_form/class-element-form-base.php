<?php 
/**
* 
*/
class ASFB_element_form
{
	
	public $schemaElement;

	private function getValue() {
        $requestValue = isset( $_GET[$this->get('type')][$this->getName()] ) ? $_GET[$this->get('type')][$this->getName()] : '' ;
        return $requestValue;
    }
	private function get($key) {
		if ( isset($this->schemaElement[$key]) ) {
			return $this->schemaElement[$key];
		} else {
			return $this->schemaElement['name'];
		}
	}

	public function select($dataElement) {
		$this->createSchema();
		$this->extractVariable($dataElement);

		if ($this->schemaElement['type'] == 'taxonomy') {
			$this->extractTermOptions();
		} else if ($this->schemaElement['type'] == 'custom_field') {
			$this->extractCustomFieldOptions();
		}

		return $this->render('select');
		// $this->debug($this->schemaElement);
	}

	public function checkbox($dataElement) {
		$this->createSchema();
		$this->extractVariable($dataElement);
		
		if ($this->schemaElement['type'] == 'taxonomy') {
			$this->extractTermOptions();
		} else if ($this->schemaElement['type'] == 'custom_field') {
			$this->extractCustomFieldOptions();
		}	

		return $this->render('checkbox');
	}

	public function textinput($dataElement) {
		$this->createSchema();
		$this->extractVariable($dataElement);
		return $this->render('textinput');
	}

	public function radio($dataElement) {
		$this->createSchema();
		$this->extractVariable($dataElement);

		if ($this->schemaElement['type'] == 'taxonomy') {
			$this->extractTermOptions();
		} else if ($this->schemaElement['type'] == 'custom_field') {
			$this->extractCustomFieldOptions();
		}

		return $this->render('radio');
	}

	public function swatchColor($dataElement) {
		$this->createSchema();
		$this->extractVariable($dataElement);

		if ($this->schemaElement['type'] == 'taxonomy') {
			$this->extractTermColor();
		} else if ($this->schemaElement['type'] == 'custom_field') {
			$this->extractCustomFieldColor();
		}

		return $this->render('swatch_color');
	}

	public function swatchText($dataElement) {
		$this->createSchema();
		$this->extractVariable($dataElement);

		if ($this->schemaElement['type'] == 'taxonomy') {
            $this->extractTermOptions();
		} else if ($this->schemaElement['type'] == 'custom_field') {
			$this->extractCustomFieldOptions();
		}
        return $this->render('swatch_text');
	}

	public function rangeNumber($dataElement) {
        $this->createSchema();
        $this->extractVariable($dataElement);

        if ($this->schemaElement['type'] == 'taxonomy') {

        } else if ($this->schemaElement['type'] == 'custom_field') {
            $this->extractCustomFieldOptions();
        }

        return $this->render('number_range');
	}

	public function checkActiveSelect() {}
	public function checkActiveCheckbox() {}

	private function getName() {
		return $this->schemaElement['name'];
	}

	private function eventOnchange() {
        if ( $this->schemaElement['search_onchange'] == true ){
            return 'onchange="form.submit()"';
        } else {
            return '';
        }
    }

	private function hasOptions() {
		if ( isset($this->schemaElement['options']) 
			&& count($this->schemaElement['options']) > 0  ) {
			return true;
		} else {
			return false;
		}
	}

	private function getOptions() {
		if ( $this->hasOptions() ) {
			return $this->schemaElement['options'];
		} else {
			return false;
		}
	}

    private function checkStatus($value, $active = '') {
	    $requestValue = isset( $_GET[$this->get('type')][$this->getName()] ) ? $_GET[$this->get('type')][$this->getName()] : '' ;
	    if ( (is_array($requestValue) && in_array($value, $requestValue)) || $requestValue == $value) {
            return $active;
        } else {
            return '';
        }
    }

    private function extractTermColor() {
    	if ( is_array($this->schemaElement['colors']) 
    		&& count($this->schemaElement['colors']) > 0 
    		&& $this->schemaElement['type'] == 'taxonomy'
    		) {

    		foreach ($this->schemaElement['colors'] as $key => $value) {
    			$term = get_term_by('id', $key, $this->schemaElement['name']);

    			$this->schemaElement['options'][$key] = array(
    				'value' => $term->term_id,
    				'label' => $term->name,
    				'color' => '#' . $value
				);
    		}
    	}
    }

    private function extractCustomFieldColor() {
    	if ( !empty($this->schemaElement['options'])
    		&& $this->schemaElement['type'] == 'custom_field'
    		) {

    		$output = array();

    		$dataOption = explode("\n", str_replace("\r", "", $this->schemaElement['options']));
    		foreach ($dataOption as $key => $cfdata) {
    		    $option = explode(':', $cfdata);
    		    if (isset($option[0]) && isset($option[1])) {
    		        $output[$option[0]] = array(
    		            'value' => trim($option[0]),
    		            'label' => trim($option[0]),
    		            'color' => trim($option[1]),
    		        );
    		    }
    		}

    		$this->schemaElement['options'] = $output;
    	}
    }

    private function extractTermOptions() {

    	if ( is_array($this->schemaElement['options']) 
    		&& count($this->schemaElement['options']) > 0 
    		&& $this->schemaElement['type'] == 'taxonomy'
    		) {

    		foreach ($this->schemaElement['options'] as $key => $value) {
    			$term = get_term_by('id', $value, $this->schemaElement['name']);
    			$this->schemaElement['options'][$key] = array(
    				'value' => $term->term_id,
    				'label' => $term->name,
    				'count' => $term->count
				);
    		}
    	}
    }

    private function extractCustomFieldOptions() {

    	if ( !empty($this->schemaElement['options'])
    		&& $this->schemaElement['type'] == 'custom_field'
    		) {

    		$output = array();

    		$dataOption = explode("\n", str_replace("\r", "", $this->schemaElement['options']));
    		foreach ($dataOption as $key => $cfdata) {
    		    $option = explode(':', $cfdata);
    		    if (isset($option[0]) && isset($option[1])) {
    		        $output[$option[0]] = array(
    		            'value' => trim($option[0]),
    		            'label' => trim($option[1]),
    		        );
    		    }
    		}

    		$this->schemaElement['options'] = $output;
    	}
    }

	private function createSchema() {
		$this->schemaElement = array(
			'label' => '',
			'name' => '',
			'options' => array(),
			'type' => '', // Taxonomy, Custom field,
			'multiple' => false,
			'min' => '',
			'max' => '',
			'step' => '',
			'date_min' => '',
			'date_max' => '',
			'colors' => '',
            'search_onchange' => false,
            'class' => '',
            'unit' => ''
		);
	}

    private function extractVariable($target) {

    	$schema = $this->schemaElement;
        foreach ($schema as $key => $value) {
            if (isset($target[$key]) ) {
                $schema[$key] = is_array($target[$key]) ? $target[$key] : trim((string)$target[$key]);
            } else {
                $schema[$key] = $value;
            }
        }
        $this->schemaElement = $schema;
    }

    private function render($pathFile) {
		ob_start();

		if ( file_exists(plugin_dir_path(__FILE__) . 'views/' . $pathFile . '.php') ) {
			include plugin_dir_path(__FILE__) . 'views/' . $pathFile . '.php';
		} else {
			echo 'Không tìm thấy file {$pathFile}';		
		}

		return ob_get_clean();
    }

    static function debug($var) {
    	echo '<pre>';
    	var_export($var);
    	echo '</pre>';
    }
}