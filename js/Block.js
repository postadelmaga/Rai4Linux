/**
 * Created with JetBrains PhpStorm.
 * User: fra
 * Date: 6/6/13
 * Time: 10:41 PM
 * To change this template use File | Settings | File Templates.
 */
function block(i, c, a) {

    this.cls = c ? c : '';
    this.id = i ? i : '';
    this.att = a ? a : Array();
    this.type = 'div';

    this._getIstance = function () {

        var block = null;

        // class mode
        if (this.cls != '' && this.id == '') {
            if ($('.' + this.cls).length)
                block = $('.' + this.cls);
        }
        // id mode
        else {
            if (this.id != '') {
                if ($("#" + this.id).length)
                    block = $("#" + id);
            }
        }
        if (!block)
            return $('<' + this.type + '/>');

        return o;
    }

    this.getBlock()
    {
        var newHtml = html ? html : null;
        var block = this._getIstance();

        if (newHtml != '') {
            if (block.length > 1) {
                block.each(function () {
                    $(this).append(newHtml);
                });
            }
            else {
                block.append(newHtml)
            }
        }

        return block;
    }
//    this.setHtml = function (myHTML) {
//
//        var e = this.getIstance();
//
//        // class mode
//        if (e.length > 1) {
//            e.each(function () {
//                $(this).append(myHTML);
//            });
//            return e;
//        }
//        // id mode
//        else {
//            return e.append(myHTML);
//        }
//    }
}