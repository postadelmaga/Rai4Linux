<div class="jumbotron subhead">
    <h1 id="titleMenu"> Rai Mobile </h1>
    <!-- ShinyStat -->
    <div class="progress progress-striped active" id="main_loader">
        <div data-percentage="0" class="bar" id="loadbar" style="width: 0%;"></div>
    </div>
    <script type="text/javascript">
        $('#titleMenu').bind('contextmenu', function (e) {
            if (confirm('Vuoi ripopolare la lista per ' + stream.currentChannel + '?')) {
                stream._loadChannel(stream.currentChannel, 1);
            }
            return false;
        });
    </script>
</div>
<?php include('blocks/forkme.php') ?>