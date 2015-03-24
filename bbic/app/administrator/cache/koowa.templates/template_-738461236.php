
        <?php echo $this->helper('bootstrap.load'); ?>
        <?php echo $this->helper('listbox.pages', array(
            'prompt' => $this->translate('All pages'),
            'name' => $el_name,
            'deselect' => $deselect,
            'selected' => $value,
            'attribs'  => $attribs,
            'types'    => $types,
            'options'  => $options
        )); ?>
            <script>
                kQuery(function($){
                    $('#s2id_<?php echo $id ?>').show();
                    $('#<?php echo $id ?>_chzn').remove();
                });
            </script>
            