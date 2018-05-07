function Loader(loader_id) {

    this.loader_id = loader_id;

    this.up = function (ch) {
        // Main Load Bar
        var loadBar = jQuery('#' + this.loader_id);
        var value = parseFloat(loadBar.attr('aria-valuenow'));
        var perc = Math.round((value + (1 / 28 * 100)) * 10) / 10; // round number (*10/10)

        if (perc >= 100) {
            perc = 100;
            loadBar.parent().fadeOut();
        }
        loadBar.attr('aria-valuenow', perc);
        loadBar.css('width', (perc) + '%');
        //loadBar.text(Math.ceil(perc));

        this.updateChannelBar(ch);
    };

    this.updateChannelBar = function (ch) {
        // Ch Bar
        var loadBarCh = jQuery('#' + this.loader_id + '_' + ch);
        var value = parseFloat(loadBarCh.attr('aria-valuenow'));
        var perc = Math.round((value + (1 / 7 * 100)) * 10) / 10; // round number (*10/10)

        if (perc >= 100) {
            perc = 100;
            loadBarCh.css('width', '0px');
            loadBarCh.attr('aria-valuenow', 0);
            loadBarCh.parent().hide();//removeClass('bar');
        }
        loadBarCh.attr('aria-valuenow', perc);
        loadBarCh.css('width', (perc) + '%');
        loadBarCh.text(Math.ceil(perc));
    };

    this.createLoader = function (ch, clickCallback, stream) {

        var chbtn = jQuery('<a>', {'id': 'btn_' + ch, 'class': "w3-button", 'href': "#"})
            .text(ch)
            .click(function (e) {
                stream.chClick(ch);
            }).mousedown(function (event) {
                switch (event.which) {
                    case 3:
                        if (confirm('Vuoi scaricare nuovamente la lista per ' + ch + '?')) {
                            stream.chLoadData(ch, true);
                        }
                        break;
                    default:
                }
            }).appendTo(jQuery('.channel_list'));


        jQuery('<div>', {'class': 'progress progress-striped active'}).append(
            jQuery('<div>', {
                'id': 'loadbar_' + ch,
                'class': 'bar',
                'aria-valuenow': 0
            })).appendTo(chbtn);

    };

    this.showLoader = function (ch) {
        jQuery('#channel_list').find('#btn_' + ch + ' .progress').show();
    };

    this.resetLoader = function (ch) {
        var load = jQuery('#loadbar_' + ch);
        load.attr('aria-valuenow', 0);
        load.css('width', 0);
    };
}