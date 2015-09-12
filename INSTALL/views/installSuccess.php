                <div style="text-align: center;margin:30px auto;">
                    <h2><?php echo _INSTALLSUCCESS ?></h2>
                    <p><?php echo _INFOPARTNERS ?></p>
                    <div id="partners" ><img src="images/loading.gif" alt="" /><br/><?php echo _WAIT ?></div>
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
                    <a href="index.php?action=deleteSession" class="button"><?php echo _ACCESS_SITE ?></a>
                </div>