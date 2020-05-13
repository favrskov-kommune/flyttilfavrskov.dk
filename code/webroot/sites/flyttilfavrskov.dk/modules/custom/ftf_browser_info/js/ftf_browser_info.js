(function ($) {
  Drupal.behaviors.FtfBrowserInfo = {
    attach: function (context, settings) {
      if (checkBrowserVersion()) {
        var self = this;
        var $body = $('body');

        var $browserInfo = $('#browser-info');
        var $browserClose = $('#browser-info-close');
        var $browserOpen = $('#browser-info-open');

        $browserOpen.addClass('shown');

        if ($browserInfo.length) {
          // If we have displayed it once, set cookie for one year
          if (getCookie("browserinfo") == "displayed") {
            if (drupalSettings.browserinfo.browserinfo_behavior == 'display_once') {
            }
            else {
              setTimeout(function () {
                $body.addClass('browser-info-show');
              }, 1000);
            }
          }

          // Check if the browser info has been displayed
          if (getCookie("browserinfo") == "") {
            setCookie("browserinfo", "displayed");

            setTimeout(function () {
              $body.addClass('browser-info-show');
            }, 1000);
          }
          // Button eventlisteners
          $browserOpen.click(function () {
            self.showInfo();
          });

          $browserClose.click(function () {
            self.hideInfo();
            setCookie("browserinfo", "displayed", 365);
          });
        }

        // Functions for opening an closing the browser-info
        this.showInfo = function () {
          $body.addClass('browser-info-show');
        };
        this.hideInfo = function () {
          $body.removeClass('browser-info-show');
        };
      }
    }
  }
})(jQuery);

/* Cookie helper functions
 *******************************/

// Get cookie
function getCookie(cname) {
  var name = cname + "=";
  var ca = document.cookie.split(';');
  for (var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1)
    }
    ;
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length)
    }
    ;
  }
  return "";
}

// Set cookie
function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays * 60 * 60 * 1000 * 24));
  var expires = "expires=" + d.toUTCString() + ";";
  var path = "path=/";
  document.cookie = cname + "=" + cvalue + "; " + expires + path;
}

/* Browser version logic
 *******************************/

function checkBrowserVersion() {
  if (navigator.userAgent.indexOf('MSIE') != -1) {
    var detectIEregexp = /MSIE (\d+\.\d+);/
  }//test for MSIE x.x
  else // if no "MSIE" string in userAgent
  {
    var detectIEregexp = /Trident.*rv[ :]*(\d+\.\d+)/
  } //test for rv:x.x or rv x.x where Trident string exists

  if (detectIEregexp.test(navigator.userAgent)) { //if some form of IE
    var ieversion = new Number(RegExp.$1) // capture x.x portion and store as a
                                          // number
    if (ieversion <= 11) {
      return true;
    }
  }
  return false;
}
