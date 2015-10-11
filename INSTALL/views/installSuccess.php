                <div style="text-align: center;margin:30px auto;">
                    <h2><?php echo $i18n['INSTALL_SUCCESS'] ?></h2>
                    <p><?php echo $i18n['INFO_PARTNERS'] ?></p>
                    <div id="partners" ><img src="media/images/loading.gif" alt="" /><br/><?php echo $i18n['WAIT'] ?></div>
                    <script type="text/javascript" >
                    function ajaxPartners(){
                        $.ajax({
                            async: true,
                            type: "POST",
                            url: "index.php?action=getPartners",
                        }).done(function(txt) {
                            $("#partners").css("display", "none");
                            $("#partners").html(txt);
                            $("#partners").fadeIn("slow");
                        });
                    }
                    $(document).ready(ajaxPartners());
                    </script>
                    <a href="index.php?action=deleteSession" class="button"><?php echo $i18n['ACCESS_SITE'] ?></a>
                </div>