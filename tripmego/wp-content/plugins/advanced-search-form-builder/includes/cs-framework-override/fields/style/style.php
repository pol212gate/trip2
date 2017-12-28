<?php
class CSFramework_Option_style extends CSFramework_Options {

    public function __construct( $field, $value = '', $unique = '' ) {
        parent::__construct( $field, $value, $unique );
    }

    public function output(){
        $option =  $this->field['options'];
        $value = $this->element_value();

        echo $this->element_before();

            if ( is_array($option) && count( $option ) > 0) {
                echo '<table>';

                foreach( $option as $key => $opt ) {
                    if( $opt['name'] == 'hr' ) {
                        echo '<tr><td colspan="2"><hr></td></tr>';
                        continue;
                    }

                    $_value = isset($value[$opt['name']]) ? $value[$opt['name']] : '' ;
                    if (empty($_value)) {
                        $_value = ( isset($opt['default']) ? $opt['default'] : '' );
                    }
                ?>

                        <tr>
                            <td  style="text-align: right"><?php echo $opt['label'] ?></td>
                            <td width="50%"><input
                                    class=""
                                    type="text" value="<?php echo $_value ?>" name="<?php echo $this->element_name() ?>[<?php echo $opt['name'] ?>]"></td>
                        </tr>
                <?php
                }

                echo '</table>';
            }
        echo $this->element_after();

    }
}

