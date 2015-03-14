<?php if($this->get('nbImages') > 0 && $this->get('blockGalleryActive')):
        if($this->get('blockGalleryLightbox') == true):
?>
            <script type="text/javascript">
                Shadowbox.init();
            </script>
        <?php endif; ?>
<section id="RL_gallery">
    <header>
        <h1 class="RL_modTitle">{{blockGalleryTitle}}</h1>
        <a class="RL_moreButton" href="index.php?file=Gallery">Plus</a>
    </header>
    <div>
        <?php foreach($this->get('blockGalleryContent') as $image): ?>
        <figure>
            <?php if($image['link'] !== '#'):
                    if($this->get('blockGalleryLightbox') == true){
                        $image['link'] = $image['src'];
                    }
                ?>
                <a href="<?php echo $image['link']; ?>" title="<?php echo $image['title']; ?>" rel="shadowbox">
            <?php endif; ?>
                    <img src="<?php echo $image['src']; ?>" alt="<?php echo $image['title']; ?>" />
            <?php if($image['link'] !== '#'): ?>
                </a>
            <?php endif; ?>
        </figure>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>