var apptha = jQuery.noConflict();
(function (apptha) {
    apptha.facebox = function (data, klass) {
        apptha.facebox.init();
        apptha.facebox.loading();
        apptha.isFunction(data) ? data.call(apptha) : apptha.facebox.reveal(data, klass)
    };
    apptha.facebox.settings = {
        loading_image: "loadinfo.gif",
        close_image: "close.png",
        image_types: ["png", "jpg", "jpeg", "gif"],
        facebox_html: '<div id="popup_overlay" style="display:none;background:black;opacity:0.8;filter: alpha(opacity=80);position:absolute;width:100%;z-index:45646"></div> <div id="facebox" style="z-index:999999;display:none;"> <div class="popup">     \t<table style="width:940px;">         <tbody>            <tr>             <td class="b"/>     \t    <td class="body"> <div class="mac-close-image">         <a href="#" class="close" title="close">             </a>       </div>     \t   <div class="appthaContent" id="appthaContent" style="width:920px;margin:0 auto;">               </div>              </td>             </tbody>       </table>     </div>   </div>'
    };
    apptha.facebox.loading = function () {
        if (apptha("#facebox .loading").length == 1) return true;
        apptha("#facebox .appthaContent").empty();
        apptha("#facebox .body").children().hide().end().append('<div class="loading" style="margin:0 auto;"></div>');
        var pageScroll = apptha.facebox.getPageScroll();
        apptha("#facebox").css({
            top: pageScroll[1] + apptha.facebox.getPageHeight() / 10 / 2,
            left: pageScroll[0]
        }).show();
        apptha("#popup_overlay").css({
            top: 0,
            left: pageScroll[0],
            height: apptha(document).height()
        }).show();
        apptha(document).bind("keydown.facebox", function (e) {
            if (e.keyCode == 27) apptha.facebox.close()
        })
    };
    apptha.facebox.reveal = function (data, klass) {
        if (klass) apptha("#facebox .appthaContent").addClass(klass);
        apptha("#facebox .appthaContent").append(data);
        apptha("#facebox .loading").remove();
        apptha("#facebox .body").children().fadeIn("normal")
    };
    apptha.facebox.close = function () {
        apptha("#popup_overlay").hide();
        apptha(document).trigger("close.facebox");
        return false
    };
    apptha(document).bind("close.facebox", function () {
        apptha(document).unbind("keydown.facebox");
        apptha("#facebox").fadeOut(function () {
            apptha("#facebox .appthaContent").removeClass().addClass("appthaContent")
        })
    });
    apptha.fn.facebox = function (settings) {
        apptha.facebox.init(settings);
        var image_types = apptha.facebox.settings.image_types.join("|");
        image_types = new RegExp("." + image_types + "apptha", "i");

        function click_handler() {
            apptha("#popup_overlay").show();
            apptha.facebox.loading(true);
            var klass = this.rel.match(/facebox\[\.(\w+)\]/);
            if (klass) klass = klass[1];
            if (this.href.match(/#/)) {
                var url = window.location.href.split("#")[0];
                var target = this.href.replace(url, "");
                apptha.facebox.reveal(apptha(target).clone().show(), klass)
            } else if (this.href.match(image_types)) {
                var image = new Image;
                image.onload = function () {
                    apptha.facebox.reveal('<div class="image"><img src="' + image.src + '" /></div>', klass)
                };
                image.src = this.href
            } else apptha.get(this.href, function (data) {
                apptha.facebox.reveal(data, klass)
            });
            return false
        }
        this.click(click_handler);
        return this
    };
    apptha.facebox.init = function (settings) {
        if (apptha.facebox.settings.inited) return true;
        else apptha.facebox.settings.inited = true;
        if (settings) apptha.extend(apptha.facebox.settings, settings);
        apptha("body").append(apptha.facebox.settings.facebox_html);
        var preload = [new Image, new Image];
        preload[0].src = apptha.facebox.settings.close_image;
        preload[1].src = apptha.facebox.settings.loading_image;
        apptha("#facebox").find(".b:first, .bl, .br, .tl, .tr").each(function () {
            preload.push(new Image);
            preload.slice(-1).src = apptha(this).css("background-image").replace(/url\((.+)\)/, "apptha1")
        });
        apptha("#facebox .close").click(apptha.facebox.close);
        apptha("#facebox .close_image").attr("src", apptha.facebox.settings.close_image)
    };
    apptha.facebox.getPageScroll = function () {
        var xScroll, yScroll;
        if (self.pageYOffset) {
            yScroll = self.pageYOffset;
            xScroll = self.pageXOffset
        } else if (document.documentElement && document.documentElement.scrollTop) {
            yScroll = document.documentElement.scrollTop;
            xScroll = document.documentElement.scrollLeft
        } else if (document.body) {
            yScroll = document.body.scrollTop;
            xScroll = document.body.scrollLeft
        }
        return new Array(xScroll, yScroll)
    };
    apptha.facebox.getPageHeight = function () {
        var windowHeight;
        if (self.innerHeight) windowHeight = self.innerHeight;
        else if (document.documentElement && document.documentElement.clientHeight) windowHeight = document.documentElement.clientHeight;
        else if (document.body) windowHeight = document.body.clientHeight;
        return windowHeight
    }
})(jQuery);