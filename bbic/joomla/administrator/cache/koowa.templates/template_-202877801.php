
        <?php echo $this->helper('bootstrap.load'); ?>
        <?php echo $this->helper('listbox.users', array(
            'prompt' => $this->translate('All Users'),
			'autocomplete' => true,
            'name' => $el_name,
            'deselect' => $deselect,
            'selected' => $value,
            'attribs'  => $attribs
        )); ?>
            <script>
                kQuery(function($){
                    $('#s2id_<?php echo $id ?>').show();
                    $('#<?php echo $id ?>_chzn').remove();
                });
            </script>
            