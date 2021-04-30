"use strict";

if (!window.ENGINE) var ENGINE = {};

/* Data */

ENGINE.data = {};

/* Elements */

ENGINE.el = {};

/* Methods */

/**
 * Add leave listener
 */
ENGINE.addLeaveListener = function() {
  $(function() {
    var form = ENGINE.el.$content.find("#k_admin_frm");
    if (form.length) {
      ENGINE.updateRichTextContent();
      var orig_data = form.find( ":input:not(.ckeditor)" ).serialize();

      form.on("submit", function() {
        window.onbeforeunload = null;
      });

      window.onbeforeunload = function() {
        ENGINE.updateRichTextContent();
        var cur_data = form.find( ":input:not(.ckeditor)" ).serialize();

        var ckeditor_dirty;
        if ( window.CKEDITOR ) {
            var key, obj;

            for ( key in CKEDITOR.instances ) {
                obj = CKEDITOR.instances[ key ];
                if( obj.checkDirty() ){
                    ckeditor_dirty = 1;
                    break;
                }
            }
        }

        if ( orig_data !== cur_data || ckeditor_dirty ) return ENGINE.lang.leave;
      };
    }
  });
};

/**
 * Add media query listeners
 */
ENGINE.addMediaQueryListeners = function() {
  this.mediaQuery = {
    currentView: "?"
  };

  if (window.matchMedia) {
    this.mediaQuery.medium = window.matchMedia("(max-width: 950px)");
    this.mediaQuery.small = window.matchMedia("(max-width: 761px)");

    this.configViewInitial(this.mediaQuery.medium, this.mediaQuery.small);

    this.mediaQuery.medium.addListener(this.configViewMedium);
    this.mediaQuery.small.addListener(this.configViewSmall);
  } else {
    this.configViewInitial({
      matches: false
    }, {
      matches: false
    });
  }
};

/**
 * Bind navigation menu toggle action
 */
ENGINE.bindNavMenuToggle = function() {
  this.el.$menuBtn.on("click", function(e) {
    e.preventDefault();

    ENGINE.toggleNavMenu();
  });
};

/**
 * Bind Magnific Popup AJAX
 * @param {jQuery Object} $elements
 * @param {Boolean}       [code]
 */
ENGINE.bindPopupAJAX = function($elements, code) {
  $elements.magnificPopup({
    callbacks: {
      parseAjax: code ? function(response) {
        response.data = '<div class="popup-blank popup-code"><div class="popup-code-content">' + response.data.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/(?:\r\n|\r|\n)/g, "<br/>") + '</div></div>';
      } : false
    },
    closeOnBgClick: false,
    preloader: false,
    type: "ajax"
  });
};

/**
 * Bind Magnific Popup gallery
 */
ENGINE.bindPopupGallery = function() {
  this.el.$content.find("#gallery-listing").magnificPopup({
    delegate: ".popup-gallery",
    gallery: {
      enabled: true
    },
    type: "image"
  });
};

/**
 * Bind Magnific Popup iframe
 * @param {jQuery Object} $elements
 * @param {Function}      [callbackOpen]
 * @param {Function}      [callbackClosed]
 * @param {String}        [mainClass]
 */
ENGINE.bindPopupIframe = function($elements, callbackBeforeOpen, callbackClosed, mainClass, modal, iframe_name, callbackOpen) {
  var config = {
    callbacks: {
      afterClose: callbackClosed,
      beforeOpen: callbackBeforeOpen,
      open: callbackOpen
    },
    mainClass: mainClass ? mainClass : "",
    closeOnBgClick: false,
    preloader: false,
    type: "iframe",
    modal: modal ? true : false,
  };
  if (modal) {
    iframe_name = iframe_name ? iframe_name : 'k-iframe';
    config.iframe = {
      markup: '<div class="mfp-iframe-scaler">' +
        '<iframe class="mfp-iframe" name="' + iframe_name + '" frameborder="0" allowfullscreen>    </iframe>' +
        '</div>',
    };
  }
  $elements.magnificPopup(config);
};

/**
 * Bind Magnific Popup image preview
 * @param {jQuery Object} $elements
 */
ENGINE.bindPopupImage = function($elements) {
  $elements.magnificPopup({
    type: "image"
  });
};

/**
 * Bind Magnific Popup inline
 * @param {jQuery Object} $elements
 */
ENGINE.bindPopupInline = function($elements) {
  $elements.magnificPopup({
    preloader: false,
    type: "inline"
  });
};


/**
 * Browse choose file action
 * @param {jQuery Object} $button
 * @param {String}        file
 */
ENGINE.browseChooseFile = function($button, file) {
  var id = $button.attr("data-kc-finder");

  $("#" + id).val(file).trigger("k_change");
  $("#" + id).val(file).trigger("change");
  $("#" + id + "_preview").attr("href", file);
  $("#" + id + "_img_preview").attr("src", file);

  $.magnificPopup.close();
};

/**
 * Close callback for KCFinder file manager modal
 */
ENGINE.browseKCFinderClose = function() {
  window.KCFinder = null;
};

/**
 * Open callback for KCFinder file manager modal
 */
ENGINE.browseKCFinderOpen = function() {
  var $this = $(this.st.el); // this is $.magnificPopup.instance
  window.KCFinder = {
    callBack: function(file) {
      ENGINE.browseChooseFile($this, file);
    }
  };
};

/**
 * Plupload bulk file upload finish
 * @param {jQuery Object} $button
 * @param {String}        result
 */
ENGINE.bulkPluploadFinish = function($button, result) {
  var msg = $.trim(result);

  if (msg.length) {
    $.magnificPopup.dialog({
      icon: "x",
      iconType: "error",
      text: msg,
      closedCallback: function() {
        $button.focus();
      }
    });
  } else {
    window.location.reload();
  }
};

/**
 * Bind relation click select action
 */
ENGINE.bindRelationSelect = function() {
  this.el.$content.find(".checklist").not(".checklist-disabled").on("change", "input", function() {
    $(this).parent().toggleClass("selected");
  });
};

/**
 * Bind sidebar click toggle actions
 */
ENGINE.bindSidebarToggles = function() {
  $("a.sidebar-toggle").on("click", function(e) {
    e.preventDefault();

    $(this).blur();

    ENGINE.toggleSidebar();
  });

  this.el.$menuContent.on("click", ".nav-heading-toggle", function() {
    var $this = $(this),
      target = $this.data("id"),
      position = ENGINE.state.collapsedGroups.indexOf(target);

    $this.parent().toggleClass("collapsed").next().slideToggle(150);

    if (position === -1) {
      ENGINE.state.collapsedGroups.push(target);
    } else {
      ENGINE.state.collapsedGroups.splice(position, 1);

      if (!ENGINE.state.collapsedGroups.length) {
        $.removeCookie("collapsed_groups");

        return;
      }
    }

    $.setCookie("collapsed_groups", ENGINE.state.collapsedGroups.join(","), 86400, null, null, document.location.protocol === "https:");
  });
};

/**
 * Bind table toggle all checkbox and row click select action
 */
ENGINE.bindTableSelect = function() {
  this.el.$table.on("change", ".checkbox-item, .checkbox-all", function() {
    var $checkboxes = ENGINE.el.$table.find(".checkbox-item").not(":disabled"),
      $this = $(this);

    if ($this.hasClass("checkbox-item")) {
      var $checkboxAll = ENGINE.el.$table.find(".checkbox-all");

      if ($checkboxes.length === $checkboxes.filter(":checked").length) {
        $checkboxAll.prop("checked", true);
      } else {
        $checkboxAll.prop("checked", false);
      }

      if ($this.prop("checked")) {
        $this.closest("tr").addClass("selected");
      } else {
        $this.closest("tr").removeClass("selected");
      }
    } else {
      $checkboxes.prop("checked", $this.prop("checked")).trigger("change");
    }
  }).on("click", "td", function(e) {
    if (e.target === this || /INPUT|LI|SPAN|STRONG/.test(e.target.nodeName)) {
      $(this).closest("tr").find(".checkbox-item").not(":disabled").prop("checked", function(i, val) {
        return !val;
      }).trigger("change");
    }
  });
};

/**
 * Bind comment item click select action
 */
ENGINE.bindCommentsSelect = function() {
  this.el.$content.find("#comments-listing").on("click", ".comment-heading", function(e) {
    if (e.target === this) {
      var $checkboxAll = ENGINE.el.$content.find(".checkbox-all"),
        $checkboxes = ENGINE.el.$content.find(".checkbox-item").not(":disabled"),
        $this = $(this);

      $this.find(".checkbox-item").not(":disabled").prop("checked", function(i, val) {
        return !val;
      }).trigger("change");

      if ($checkboxes.length === $checkboxes.filter(":checked").length) {
        $checkboxAll.prop("checked", true);
      } else {
        $checkboxAll.prop("checked", false);
      }
    }
  });

  this.el.$content.find("#comments-listing").on("change", ".checkbox-all", function(e) {
    var $this = $(this),
      $checkboxes = ENGINE.el.$content.find(".checkbox-item").not(":disabled"),
      checked = $this.prop("checked");

    $checkboxes.prop("checked", checked);
  });
};

/**
 * Bind gallery item click select action
 */
ENGINE.bindGallerySelect = function() {
  this.el.$content.find("#gallery-listing").on("click", ".gallery-item:not(.gallery-folder)", function(e) {
    var $checkboxAll = ENGINE.el.$content.find(".checkbox-all"),
      $checkboxes = ENGINE.el.$content.find(".checkbox-item").not(":disabled"),
      $this = $(this);

    if (e.target === this || /DIV|STRONG/.test(e.target.nodeName)) {
      $this.find(".checkbox-item").not(":disabled").prop("checked", function(i, val) {
        $this.toggleClass("selected");

        return !val;
      }).trigger("change");
    } else if (e.target.nodeName === "INPUT" && !$(e.target).is(":disabled")) {
      $this.toggleClass("selected");
    }

    if ($checkboxes.length === $checkboxes.filter(":checked").length) {
      $checkboxAll.prop("checked", true);
    } else {
      $checkboxAll.prop("checked", false);
    }
  });

  this.el.$content.find("#gallery-listing").on("change", ".checkbox-all", function(e) {
    var $this = $(this),
      $checkboxes = ENGINE.el.$content.find(".checkbox-item").not(":disabled"),
      checked = $this.prop("checked");

    $checkboxes.prop("checked", checked).trigger("change").closest(".gallery-item").toggleClass("selected", checked);
  });
};

/**
 * Bind scroll to top click scroll action
 */
ENGINE.bindTopScroll = function() {
  $("#top").on("click", function(e) {
    e.preventDefault();

    $(this).blur();

    $("html, body, #scroll-content").animate({
      scrollTop: 0
    }, 400);
  });
};

/**
 * Configure initial view
 * @param {MediaQueryList Object} medium
 * @param {MediaQueryList Object} small
 */
ENGINE.configViewInitial = function(medium, small) {
  if (small.matches) {
    this.mediaQuery.currentView = "S";

    this.createActionPopovers();
  } else if (medium.matches) {
    this.mediaQuery.currentView = "M";

    this.createTooltips($("body"), ".tt");

    this.createTooltips(this.el.$navCount);

    this.createTooltips(this.el.$collapseTooltips);
  } else {
    this.mediaQuery.currentView = "L";

    this.createTooltips($("body"), ".tt");

    this.createTooltips(this.el.$navCount);

    this.createTooltips(this.el.$tabErrors);
  }
};

/**
 * Configure medium view
 * @param {MediaQueryList Object} mediaQuery
 */
ENGINE.configViewMedium = function(mediaQuery) {
  if (mediaQuery.matches) {
    ENGINE.mediaQuery.currentView = "M";

    ENGINE.destroyTooltips(ENGINE.el.$tabErrors);

    ENGINE.createTooltips(ENGINE.el.$collapseTooltips);
  } else {
    ENGINE.mediaQuery.currentView = "L";

    ENGINE.createTooltips(ENGINE.el.$tabErrors);

    ENGINE.destroyTooltips(ENGINE.el.$collapseTooltips);
  }
};

/**
 * Configure small view
 * @param {MediaQueryList Object} mediaQuery
 */
ENGINE.configViewSmall = function(mediaQuery) {
  if (mediaQuery.matches) {
    ENGINE.mediaQuery.currentView = "S";

    ENGINE.createActionPopovers();

    ENGINE.destroyTooltips($("body"));

    ENGINE.destroyTooltips(ENGINE.el.$navCount);

    ENGINE.destroyTooltips(ENGINE.el.$collapseTooltips);
  } else {
    ENGINE.mediaQuery.currentView = "M";

    ENGINE.destroyActionPopovers();

    ENGINE.createTooltips($("body"), ".tt");

    ENGINE.createTooltips(ENGINE.el.$navCount);

    ENGINE.createTooltips(ENGINE.el.$collapseTooltips);
  }
};

/**
 * Create list action popovers
 */
ENGINE.createActionPopovers = function() {
  this.el.$content.popover({
    container: "body",
    html: true,
    placement: "top",
    selector: ".btn-actions",
    trigger: "focus",
    content: function() {
      var $this = $(this),
        $content = $this.siblings("a"),
        $actions = $content.filter(".approve-comment, .disapprove-comment, .up, .down").add($this.parent().siblings(".col-up-down").children(".up, .down"));

      if ($actions.length) {
        return $('<div class="popover-actions"></div>').append($actions.clone()).append('<span class="popover-actions-sep"></span>').append($content.not($actions).clone());
      } else {
        return $('<div class="popover-actions"></div>').append($content.clone());
      }
    }
  });
};

/**
 * Create edit help popovers
 */
ENGINE.createHelpPopovers = function() {
  this.el.$content.parent().popover({
    container: "body",
    placement: "top",
    selector: ".field-help",
    trigger: "hover focus"
  });
};

/**
 * Create tooltips
 * @param {jQuery Object} $element
 * @param {String}        [selector]
 */
ENGINE.createTooltips = function($element, selector) {
  $element.doOnce(function() {
    $(this).tooltip({
      animation: false,
      container: "body",
      selector: selector ? selector : false
    });
  });
};

/**
 * Destroy list action popovers
 */
ENGINE.destroyActionPopovers = function() {
  this.el.$content.popover("destroy");
};

/**
 * Destroy tooltips
 * @param {jQuery Object} $element
 */
ENGINE.destroyTooltips = function($element) {
  $element.doOnce(function() {
    $(this).tooltip("destroy");
  });
};

/*!
 * Check for overflow-scrolling CSS property support
 * @author Hay Kranen <https://github.com/hay/>
 * @return {Boolean}
 */
ENGINE.hasOverflowScrolling = function() {
  if (!window.getComputedStyle) return false;

  var computedStyle, i,
    div = document.createElement("div"),
    hasIt = false,
    prefixes = ["moz", "ms", "o", "webkit"];

  document.body.appendChild(div);

  for (i = 0; i < prefixes.length; i++) div.style[prefixes[i] + "OverflowScrolling"] = "touch";

  div.style.overflowScrolling = "touch";

  computedStyle = window.getComputedStyle(div);

  hasIt = !!computedStyle.overflowScrolling;

  for (i = 0; i < prefixes.length; i++) {
    if (!!computedStyle[prefixes[i] + "OverflowScrolling"]) {
      hasIt = true;
      break;
    }
  }

  div.parentNode.removeChild(div);

  return hasIt;
};

/**
 * Set Magnific Popup default settings
 */
ENGINE.setMagnificPopupDefaults = function() {
  if (!$.magnificPopup) return;

  $.extend(true, $.magnificPopup.defaults, {
    ajax: {
      tError: ENGINE.lang.popup.ajaxError
    },
    gallery: {
      tCounter: ENGINE.lang.popup.counter,
      tNext: ENGINE.lang.popup.next,
      tPrev: ENGINE.lang.popup.previous
    },
    image: {
      titleSrc: "data-popup-title",
      tError: ENGINE.lang.popup.imgError
    },
    mainClass: "mfp-fade",
    removalDelay: 210,
    tClose: ENGINE.lang.popup.close,
    tLoading: ENGINE.lang.popup.loading
  });
};

/**
 * Slide up, fade out, and hide the element, then optionally call the callback function
 * @param {jQuery Object} $element
 * @param {Number|String} speed
 * @param {Function}      [callback]
 */
ENGINE.slideFadeHide = function($element, speed, callback) {
  $element.removeClass("in");

  setTimeout(function() {
    $element.slideUp(speed, function() {
      if ($.isFunction(callback)) callback.call(this);
    });
  }, speed);
};

/**
 * Slide down, fade in, and show the element, then optionally call the callback function
 * @param {jQuery Object} $element
 * @param {Number|String} speed
 * @param {Function}      [callback]
 */
ENGINE.slideFadeShow = function($element, speed, callback) {
  $element.slideDown(speed, function() {
    $(this).addClass("in");

    if ($.isFunction(callback)) {
      setTimeout(function() {
        callback.call(this);
      }, speed);
    }
  });
};

/**
 * Toggle sidebar navigation menu
 */
ENGINE.toggleNavMenu = $.debounce(function() {
  this.el.$menuBtn.toggleClass("toggled");

  this.el.$menuContent.animate({
    height: "toggle"
  }, 400, function() {
    var $this = $(this);

    if (!$this.is(":visible")) $this.removeAttr("style");
  });
}, 200, true);

/**
 * Toggle sidebar
 */
ENGINE.toggleSidebar = $.debounce(function() {
  if (this.el.$sidebar.hasClass("collapsed")) {
    this.el.$sidebar.removeClass("collapsed");

    $.removeCookie("collapsed_sidebar");
  } else {
    this.el.$sidebar.addClass("collapsed");

    $.setCookie("collapsed_sidebar", "1", 86400, null, null, document.location.protocol === "https:");
  }
}, 200, true);

/**
 * Update rich text form content
 */
ENGINE.updateRichTextContent = function() {
  if (window.CKEDITOR) {
    var key, obj;

    for (key in CKEDITOR.instances) {
      obj = CKEDITOR.instances[key];

      if (CKEDITOR.instances.hasOwnProperty(key)) obj.updateElement();
    }
  }

  if (window.nicEditors) {
    var i = nicEditors.editors.length - 1;

    do {
      try {
        nicEditors.editors[i].nicInstances[0].saveContent();
      } catch (e) {}

      i--;
    } while (i > -1);
  }
};


/**
 * Initialize application
 */
ENGINE.init = function() {
  $(function() {
    ENGINE.data.overflowScrolling = ENGINE.hasOverflowScrolling();

    ENGINE.el.$collapseTooltips = $(".tt-collapse");
    ENGINE.el.$content = $("#content");
    ENGINE.el.$menuContent = $(".sidebar");
    ENGINE.el.$sidebar = $(".main-sidebar");
    ENGINE.el.$tabs = $("#tabs");
    ENGINE.el.$table = ENGINE.el.$content.find(".table-list");
    ENGINE.el.$menuBtn = ENGINE.el.$sidebar.find(".btn-primary.btn-menu");
    ENGINE.el.$navCount = ENGINE.el.$sidebar.find(".nav-count");
    ENGINE.el.$tabErrors = ENGINE.el.$tabs.find(".tab-error");

    ENGINE.state.collapsedGroups = $.hasCookie("collapsed_groups") ? $.getCookie("collapsed_groups").split(",") : [];

    ENGINE.setMagnificPopupDefaults();
    ENGINE.addMediaQueryListeners();
    ENGINE.bindSidebarToggles();
    ENGINE.bindNavMenuToggle();
    ENGINE.createHelpPopovers();
    ENGINE.bindTopScroll();
    ENGINE.bindTableSelect();
    ENGINE.addLeaveListener();
    ENGINE.bindPopupAJAX(ENGINE.el.$sidebar.find(".popup-ajax"), true);
    ENGINE.bindPopupInline(ENGINE.el.$content.find(".popup-inline"));
  });
};

ENGINE.lang = {
  leave: "Any unsaved changes will be lost.",

  popup: {
    ajaxError: "<a href='%url%' target='_blank'>The content</a> could not be loaded.",
    close: "Close (Esc)",
    counter: "%curr% of %total%",
    imgError: "<a href='%url%' target='_blank'>The image</a> could not be loaded.",
    loading: "Loading\u2026",
    next: "Next",
    previous: "Previous"
  }
};

ENGINE.state = {};

ENGINE.init();
