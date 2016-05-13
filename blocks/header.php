<div class="page-header">
    <h1 id="titleMenu"> Rai Mobile </h1>
    <!-- ShinyStat -->
    <div class="progress progress-striped active" id="main_loader">
        <div id="loadbar" class="progress-bar" role="progressbar" aria-valuenow="0" style="width: 0%;"></div>
    </div>
    <?php include('blocks/forkme.php') ?>
</div>
<script type="text/javascript">
    $('#titleMenu').bind('contextmenu', function (e) {
        if (confirm('Vuoi ripopolare la lista per ' + stream.currentChannel + '?')) {
            stream._loadChannel(stream.currentChannel, true);
        }
        return false;
    });
</script>
