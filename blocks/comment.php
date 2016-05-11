<?php if ($_SERVER['HTTP_HOST'] != 'localhost'): ?>
    <footer>
        <div class="shiny">
            <script type="text/javascript"
                    src="http://codice.shinystat.com/cgi-bin/getcod.cgi?USER=postadelmaga"></script>
            <script>
                (function (i, s, o, g, r, a, m) {
                    i['GoogleAnalyticsObject'] = r;
                    i[r] = i[r] || function () {
                            (i[r].q = i[r].q || []).push(arguments)
                        }, i[r].l = 1 * new Date();
                    a = s.createElement(o),
                        m = s.getElementsByTagName(o)[0];
                    a.async = 1;
                    a.src = g;
                    m.parentNode.insertBefore(a, m)
                })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

                ga('create', 'UA-8551093-3', 'byethost7.com');
                ga('send', 'pageview');

            </script>
            <!-- begin htmlcommentbox.com -->
            <div id="HCB_comment_box"><a href="http://www.htmlcommentbox.com">Comment Form</a> is loading
                comments...
            </div>
            <link rel="stylesheet" type="text/css"
                  href="//www.htmlcommentbox.com/static/skins/bootstrap/twitter-bootstrap.css?v=0"/>
            <script type="text/javascript" id="hcb"> /*<!--*/
                if (!window.hcb_user) {
                    hcb_user = {};
                }
                (function () {
                    var s = document.createElement("script"), l = (hcb_user.PAGE || "" + window.location), h = "//www.htmlcommentbox.com";
                    s.setAttribute("type", "text/javascript");
                    s.setAttribute("src", h + "/jread?page=" + encodeURIComponent(l).replace("+", "%2B") + "&opts=16862&num=10");
                    if (typeof s != "undefined") document.getElementsByTagName("head")[0].appendChild(s);
                })();
                /*-->*/ </script>
            <!-- end htmlcommentbox.com -->
        </div>
        <!-- ShinyStat -->
    </footer>
<?php endif; ?>
