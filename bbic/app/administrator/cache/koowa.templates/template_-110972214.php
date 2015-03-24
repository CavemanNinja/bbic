
        <?php echo $this->helper('bootstrap.load'); ?>
        <?php echo $this->helper('listbox.pagecategories', array(
            'prompt' => $this->translate('All Categories'),
            'page' => $page,
			'select2' => true,
			'tree' => $tree,
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
            
            <script>
                kQuery(function($){
                    $('#docman_page_select2').change(function(e) {
                        var url = e.val ? '/bbic/joomla/administrator/index.php?option=com_docman&view=categories&format=json' + '&page=' + e.val : '/bbic/joomla/administrator/index.php?option=com_docman&view=categories&format=json';
                        $.ajax(url, {
                            success: function(data) {
                                var select = $('#docman_page_categories_select2');
                                select.empty();
                                $.each(data.entities, function(idx, el) {
                                    select.append('<option value="'+el.id+'">'+el.hierarchy_title+'</option>');
                                });
                                // Reset selection
                                select.select2('val', '');
                            }
                        });
                    });
                });
            </script>