<?php
class ASFB_formSearchElement
{
    public $searchElementTemplate;
    public $getRequest;

    function __construct()
    {
        $path = plugin_dir_path(__DIR__) . 'template-field/';
        $this->searchElementTemplate = apply_filters('advanced-search-form-builder-path-template-field', $path);
        $this->getRequest = (isset($_GET) ? $_GET : array());
    }

    public function select($args)
    {
        $data = $this->extractVariable(array(
            'label' => '',
            'name' => '',
            'options' => array(),
            'type' => 'taxonomy'
        ), $args);

        if ($data['type'] == 'taxonomy') {
            $data['options'] = $this->generalTermOption($data['options']);
        } else if ($data['type'] == 'custom_field') {
            $data['options'] = $this->generalCustomFieldOption($data['options']);
        }

        $args = $data;
        ob_start();
        include $this->searchElementTemplate . 'select.tpl.php';
        return ob_get_clean();
    }

    public function checkbox($args)
    {
        $data = $this->extractVariable(array(
            'label' => '',
            'name' => '',
            'options' => array(),
            'type' => 'taxonomy'
        ), $args);

        if ($data['type'] == 'taxonomy') {
            $data['options'] = $this->generalTermOption($data['options']);
        } else if ($data['type'] == 'custom_field') {
            $data['options'] = $this->generalCustomFieldOption($data['options']);
        }

        $args = $data;
        ob_start();
        include $this->searchElementTemplate . 'checkbox.tpl.php';
        return ob_get_clean();
    }
    public function radio($args)
    {
        $data = $this->extractVariable(array(
            'label' => '',
            'name' => '',
            'options' => array(),
            'type' => 'taxonomy'
        ), $args);

        if ($data['type'] == 'taxonomy') {
            $data['options'] = $this->generalTermOption($data['options']);
        } else if ($data['type'] == 'custom_field') {
            $data['options'] = $this->generalCustomFieldOption($data['options']);
        }

        $args = $data;
        ob_start();
        include $this->searchElementTemplate . 'radio.tpl.php';
        return ob_get_clean();
    }
    public function swatchColor($args)
    {


        $data = $this->extractVariable(array(
            'label' => '',
            'name' => '',
            'options' => array(),
            'colors' => array(),
            'multiple' => false,
            'type' => 'taxonomy',
        ), $args);
        if ($data['type'] == 'taxonomy') {
            if (is_array($data['colors'])) {
                foreach ($data['colors'] as $key => $color) {
                    $_term = get_term($key);

                    $data['options'][$_term->term_id] = array(
                        'value' => $_term->term_id,
                        'label' => $_term->name,
                        'color' => $color,
                        'term_data' => array(
                            'term_id' => $_term->term_id,
                            'taxonomy' => $_term->taxonomy,
                            'name' => $_term->name,
                            'description' => $_term->description,
                            'count' => $_term->count,
                            'parent' => $_term->parent,
                        )
                    );
                }
            }

        } else if ($data['type'] == 'custom_field') {
            $data['options'] = $this->generalCustomFieldOption($data['options'], 'color');
        }
        $args = $data;
        ob_start();
        include $this->searchElementTemplate . 'swatch-color.tpl.php';
        return ob_get_clean();
    }
    public function swatchText($args)
    {
        $data = $this->extractVariable(array(
            'label' => '',
            'name' => '',
            'options' => array(),
            'type' => 'taxonomy',
            'multiple' => false,
        ), $args);

        if ($data['type'] == 'taxonomy') {
            $data['options'] = $this->generalTermOption($data['options']);
        } else if ($data['type'] == 'custom_field') {
            $data['options'] = $this->generalCustomFieldOption($data['options']);
        }

        $args = $data;

        ob_start();
        include $this->searchElementTemplate . 'swatch-text.tpl.php';
        return ob_get_clean();
    }
    public function textBox($args)
    {
        $data = $this->extractVariable(array(
            'label' => '',
            'name' => '',
            'type' => '',
            'options' => array(),
        ), $args);

        $args = $data;
        ob_start();
        include $this->searchElementTemplate . 'text.tpl.php';
        return ob_get_clean();
    }
    public function rangeNumber($args)
    {
        $data = $this->extractVariable(array(
            'label' => '',
            'name' => '',
            'min' => 0,
            'type' => '',
            'max' => 10000,
            'step' => 100
        ), $args);

        $args = $data;
        ob_start();
        include $this->searchElementTemplate . 'range-number.tpl.php';
        return ob_get_clean();
    }
    public function datePicker($args)
    {
        $data = $this->extractVariable(array(
            'label' => '',
            'name' => '',
            'type' => '',
            'datepicker_min' => '',
            'datepicker_max' => ''
        ), $args);

        $args = $data;
        ob_start();
        include $this->searchElementTemplate . 'datepicker.tpl.php';
        return ob_get_clean();
    }

    private function extractVariable($schema, $target) {
        foreach ($schema as $key => $value) {
            if (isset($target[$key]) ) {
                $schema[$key] = is_array($target[$key]) ? $target[$key] : trim((string)$target[$key]);
            } else {
                $schema[$key] = $value;
            }
        }
        return $schema;
    }

    private function generalTermOption($termIds)
    {
        $output = array();

        if (is_array($termIds)) {
            foreach ($termIds as $id) {
                $_term = get_term($id);
                $output[$_term->term_id] = array(
                    'value' => $_term->term_id,
                    'label' => $_term->name,
                    'term_data' => array(
                        'term_id' => $_term->term_id,
                        'taxonomy' => $_term->taxonomy,
                        'name' => $_term->name,
                        'description' => $_term->description,
                        'count' => $_term->count,
                        'parent' => $_term->parent,
                    )
                );
            }
        }

        return $output;
    }

    private function generalCustomFieldOption($args, $type = '')
    {
        $output = array();

        $dataOption = explode("\n", str_replace("\r", "", $args));
        foreach ($dataOption as $key => $cfdata) {
            $option = explode(':', $cfdata);
            if (isset($option[0]) && isset($option[1])) {
                $output[$option[0]] = array(
                    'value' => trim($option[0]),
                    'label' => trim($option[1]),
                );
            }

            if ($type == 'color' && isset($option[2])) {
                $output[$option[0]]['color'] = trim($option[2]);
            } else {
                $output[$option[0]]['color'] = '';
            }
        }

        return $output;
    }

    private function getValueRequestArray($type, $name, $default = '') {
        if (isset($this->getRequest[$type]) && isset($this->getRequest[$type][$name])) {
            return (array)$this->getRequest[$type][$name];
        } else {
            return (array)$default;
        }
    }

    private function statusChecked($key, $name, $value) {
        $valueRequest = $this->getValueRequestArray($key, $name);

        if (in_array($value, $valueRequest)) {
            return 'checked';
        } else {
            return '';
        }
    }
    private function statusSelected($key, $name, $value) {
        $valueRequest = $this->getValueRequestArray($key, $name);

        if (in_array($value, $valueRequest)) {
            return 'selected';
        } else {
            return '';
        }
    }
}