<?php
class CSFramework_Option_swatch_color extends CSFramework_Options {

    public function __construct( $field, $value = '', $unique = '' ) {
        parent::__construct( $field, $value, $unique );
    }

    public function output(){
        ob_start();
        echo $this->element_before();

        $taxonomies = get_taxonomies(array(), 'objects');
        $oldData = (array)$this->element_value();

        $terms = array();
        if (!isset($oldData['taxonomy'])) {
            $oldData['taxonomy'] = '';
        }
        
        if (isset($oldData['terms']) && is_array($oldData['terms'])) {
            $oldValue = implode(',', $oldData['terms']);
        } else {
            $oldValue = ''; 
        }

    ?>
        <div class="blockSwatchColor">
            <span style="display: none;" class="jsonData"><?php echo json_encode($oldData) ?></span>
            <p class="">
                <select
                    style="width: 100%" 
                    name="<?php echo $this->element_name() ?>[taxonomy]" 
                    id="<?php echo $this->element_name() ?>[taxonomy]"
                    class="taxonomySelect"
                >
                    <option value="">Select a Taxonomy</option>
                    <?php foreach ($taxonomies as $key => $value) : ?>
                        <option <?php echo $this->checked( $oldData['taxonomy'], $value->name, 'selected' ) ?> 
                                value="<?php echo $value->name ?>"><?php echo $value->label ?></option>
                    <?php endforeach ?>
                </select>                
            </p>

            <p>
                <select 
                    name="<?php echo $this->element_name() ?>[terms][]" 
                    class="chosen termsSelect" 
                    id=""
                    style="width: 100%" 
                    multiple="" 
                >
                    <option value="">Select a term</option>
                </select>
            </p>

            <div>
                <table class="tableInputColor">
                    
                </table>
            </div>
        </div>
        
    <?php 

        echo $this->element_after();

        echo ob_get_clean();
    }
}