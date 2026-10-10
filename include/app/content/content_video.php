<style>
    video-compat-skin {
        width: 90% !important;
        margin: auto;
        --media-border-color: transparent;
    }
</style>
<video-player>
  <video-compat-skin style="display: block; width: 100%; aspect-ratio: 16 / 9;">
    <video src="process.php?r=get_content&mode=video&cid=<?php echo(filter_var($_GET['id'], FILTER_VALIDATE_INT)); ?>" playsinline></video>
  </video-compat-skin>
</video-player>
<script type="module" src="/include/js/video.js/video.js"></script>