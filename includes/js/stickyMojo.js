(function($) {
  $.fn.extend({
    stickyMojo: function(options) {

      var settings = $.extend({
        'footerID': '',
        'contentID': '',
        'orientation': $(this).css('float')
      }, options);

      var sticky = {
        'el': $(this),
        'stickyLeft': $(settings.contentID).outerWidth() + $(settings.contentID).offset.left,
        'stickyTop2': $(this).offset().top,
        'stickyHeight': $(this).outerHeight(true),
        'contentHeight': $(settings.contentID).outerHeight(),
        'win': $(window),
        'breakPoint': $(this).outerWidth(true) + $(settings.contentID).outerWidth(true),
        'marg': parseInt($(this).css('margin-top'), 10)
      };

      var errors = checkSettings();
      cacheElements();

      return this.each(function() {
        buildSticky();
      });

      function buildSticky() {
        if (!errors.length) {
          sticky.el.css('left', sticky.stickyLeft);
          
          sticky.win.bind({
            'scroll': stick,
            'resize': function() {
              sticky.el.css('left', sticky.stickyLeft);
              stick();
            }
          });
        } else {
          if (console && console.warn) {
            console.warn(errors);
          } else {
            alert(errors);
          }
        }
      }

      // Caches the footer and content elements into jquery objects
      function cacheElements() {
        settings.footerID = $(settings.footerID);
        settings.contentID = $(settings.contentID);
      }

      //  Calcualtes the limits top and bottom limits for the sidebar
      function calculateLimits() {
      	  if(sticky.stickyHeight < 1000){
      	  	  return {
      	  	  	  limit: settings.footerID.offset().top - 501,
      	  	  	  windowTop: sticky.win.scrollTop(),
      	  	  	  stickyTop: sticky.stickyTop2 - sticky.marg
          	  }
      	  }
      	  
      }

      // Sets sidebar to fixed position
      function setFixedSidebar() {
        if(settings.orientation === "left"){
      	  sticky.el.css({
          position: 'fixed',
          top: 0
        });
        } else if(settings.orientation === "right"){
        	sticky.el.css({
        		position: 'fixed',
        		'right': ''
        	});
        }
      }

      // Determines the sidebar orientation and sets margins accordingly
      function checkOrientation() {
        if (settings.orientation === "left") {
          settings.contentID.css('margin-left', '150px');
        } else if(settings.orientation === "right"){
          sticky.el.css({
          'margin-right': '0'
          });
        }
      }

      // sets sidebar to a static positioned element
      function setStaticSidebar() {
      	  if (settings.orientation === "left"){
      	  	  sticky.el.css({
      	  	  	'position': 'static',
      	  	  	'margin-left': '0'
          });
          	  settings.contentID.css({
          	  	'margin-left':'0'});
          }else if(settings.orientation === "right"){
          	  sticky.el.css({
          	  	'position':'static',
          	  	'margin-right':'0',
          	  	'margin-left':'0',
          	  	'top':0
          	  });
          	  settings.contentID.css({
          	  	'margin-right':'0'
          	  });
          }
      }

      // initiated to stop the sidebar from intersecting the footer
      function setLimitedSidebar(diff) {
      	  sticky.el.css({
          top: diff
        });
      }

      //determines whether sidebar should stick and applies appropriate settings to make it stick
      function stick() {
        var tops = calculateLimits();
        var hitBreakPoint = tops.stickyTop < tops.windowTop && (sticky.win.width() >= sticky.breakPoint);

        if (hitBreakPoint) {
          setFixedSidebar();
          checkOrientation();
        } else {
          setStaticSidebar();
        }
        if (tops.limit < tops.windowTop) {
          var diff = tops.limit - tops.windowTop+5;
          var variable = 0;
          if(sticky.stickyHeight<1000){
          	  variable = sticky.stickyHeight;
          }
          setLimitedSidebar(diff);
        }
      }

      // verifies that all settings are correct
      function checkSettings() {
        var errors = [];
        for (var key in settings) {
          if (!settings[key]) {
            errors.push(settings[key]);
          }
        }
        ieVersion() && errors.push("NO IE 7");
        return errors;
      }

      function ieVersion() {
        if(document.querySelector) {
          return false;
        }
        else {
          return true;
        }
      }
    }
  });
})(jQuery);