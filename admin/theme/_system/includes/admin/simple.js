"use strict";

if (!window.ENGINE) var ENGINE = {};

/* Methods */

/**

 * Create tooltips

 */

ENGINE.createTooltips = function() {

  $("body").tooltip({

    animation: false,

    container: "body",

    selector: ".tt"

  });

};

/**

 * Initialize application

 */

ENGINE.init = function() {

  $(function() {

    ENGINE.createTooltips();

  });

};

ENGINE.init();
