
<?php
    // choix de l'éditeur
    if ($GLOBALS['nuked']['editor_type'] == 'cke') : //ckeditor
?>
                    <script type="text/javascript" src="media/ckeditor/ckeditor.js"></script>
                    <script type="text/javascript" src="media/ckeditor/config.js"></script>
                    <script type="text/javascript">
                    //<![CDATA[
                        <?php echo ($GLOBALS['nuked']['video_editeur'] == 'on') ? 'CKEDITOR.config.extraPlugins = "Video";' : '' ?>
                        CKEDITOR.config.scayt_sLang = "<?php echo ($GLOBALS['language'] == 'french') ? 'fr_FR' : 'en_US' ?>";
                        <?php echo ($GLOBALS['nuked']['scayt_editeur'] == 'on') ? 'CKEDITOR.config.scayt_autoStartup = "true";' : '' ?>
                        CKEDITOR.replaceAll(function(textarea,config){
                            if (textarea.className!='editor') return false;
                            CKEDITOR.config.toolbar = 'Full';
                            CKEDITOR.config.autoGrow_onStartup = true;
                            CKEDITOR.config.autoGrow_maxHeight = 200;
                            CKEDITOR.configlanguage = '<?php echo substr($GLOBALS['language'], 0, 2) ?>';
                            <?php echo (! empty($GLOBALS['bgcolor4'])) ? 'CKEDITOR.config.uiColor = "'. $GLOBALS['bgcolor4'] .'";' : ''; ?>
                            CKEDITOR.config.allowedContent=
                                'p h1 h2 h3 h4 h5 h6 blockquote tr td div a span{text-align,font-size,font-family,font-style,color,background-color,display};' +
                                'img[!src,alt,width,height,class,id,style,title,border];' +
                                'strong s em u strike sub sup ol ul li br caption thead  hr big small tt code del ins cite q address section aside header;' +
                                'div[class,id,style,title,align]{page-break-after,width,height,background};' +
                                'a[!href,accesskey,class,id,name,rel,style,tabindex,target,title];' +
                                'table[align,border,cellpadding,cellspacing,class,id,style];' +
                                'td[colspan, rowspan];' +
                                'th[scope];' +
                                'pre(*);' +
                                'span[id, style];'
<?php
        if ($GLOBALS['nuked']['video_editeur'] == 'on') : ?>
                                    + 'object[width,height,data,type];'
                                    + 'param[name,value];'
                                    + 'embed[width,height,src,type,allowfullscreen,allowscriptaccess];'
<?php
        endif
?>
                                ;
                            });
<?php
        if ($_REQUEST['file'] == 'Forum' && ($_REQUEST['op'] == 'edit_forum' || $_REQUEST['op'] == 'add_forum')) :
?>
                            CKEDITOR.config.autoParagraph = false;
                            CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
<?php
        endif;

        echo ConfigSmileyCkeditor();
?>
                    //]]>
                    </script>
<?php
    elseif ($GLOBALS['nuked']['editor_type'] == 'tiny') : //tinymce
?>
                    <script type="text/javascript" src="media/tinymce/tinymce.min.js"></script>
                    <script type="text/javascript">
                        //<![CDATA[
                        tinymce.init({
                            selector: "textarea.editor",
                            language : 'fr_FR',
                            plugins: [
                                "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                                "searchreplace wordcount visualblocks visualchars code fullscreen",
                                "insertdatetime media nonbreaking save table contextmenu directionality",
                                "emoticons paste textcolor responsivefilemanager youtube"
                            ],
                            image_advtab: true,
                            toolbar1: "insertfile undo redo | styleselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | media youtube link image emoticons responsivefilemanager | preview",
                            /* toolbar2: "print preview media | forecolor backcolor emoticons | link image", */

                           external_filemanager_path: 'media/filemanager/',
                           filemanager_title: 'Gestion des fichiers',
                           external_plugins: { 'filemanager' : '../filemanager/plugin.min.js' }

                         });
                    //]]>
                    </script>
<?php
    endif
?>
                </div>
            </div>
        <!-- End Main Content -->

        </div>
    <!-- End #body-wrapper -->
    </div>