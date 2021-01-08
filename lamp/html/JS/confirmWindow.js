function confirmDelete(){
    var del=confirm("Are you sure you want to DELETE this?\n");
    return del;
}


function confirmProp(){
    var doc = "NOT OK";
    var val = ['array(bool)','array(float)','array(int)','array(str)','bool','float','int','str'];
    while (!val.includes(doc)) {
        doc = prompt("Please enter the Data Type:\n array(bool), array(float), array(int), array(str), bool, float, int, str",""); 
        if (doc === null) {
            return false;
        }
    }
    
    document.cookie = 'propertyType=' + doc;
}

function MarkAsChanged(){
    $(this).addClass("changed");
}
$(":input").blur(MarkAsChanged).change(MarkAsChanged);

$("input[type=button]").click(function(){
    $(":input:not(.changed)").attr("disabled", "disabled");
    $("h1").text($("#test").serialize());
});


function newLabel(){
    var doc = "";
    while (doc == "") {
        var doc = prompt("Please enter a label for the new version");
        if (doc === null) {
            return false;
        } else {
            document.cookie = 'newLabel=' + doc;
        }
    }
    
}


function updateLabel(){
    var doc = "";
        var doc = prompt("Update label if you wish");
        if (doc === null) {
            return false;
        } else {
            document.cookie = 'newLabel=' + doc;
        }
    
}


function hideDiv(id) {
    var x = document.getElementById(id);
    var y = document.getElementById("button_" + id);
    if (x.style.display === "block") {
      x.style.display = "none";
      y.style.backgroundColor = "#245e94";
    } else {
      x.style.display = "block";
      y.style.backgroundColor = "#f15d22";
     
    }
  }


// When document is ready...
$(document).ready(function() {

    // If cookie is set, scroll to the position saved in the cookie.
    if ( $.cookie("scroll") !== null ) {
        $(document).scrollTop( $.cookie("scroll") );
    }

    // When a button is clicked...
    $('#test').on("post", function() {
    
        // Set a cookie that holds the scroll position.
        $.cookie("scroll", $(document).scrollTop() );
    
    });

});





/*!
 * jQuery Cookie Plugin v1.3
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2011, Klaus Hartl
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/GPL-2.0
 */
(function ($, document, undefined) {

    var pluses = /\+/g;

    function raw(s) {
        return s;
    }

    function decoded(s) {
        return decodeURIComponent(s.replace(pluses, ' '));
    }

    var config = $.cookie = function (key, value, options) {

        // write
        if (value !== undefined) {
            options = $.extend({}, config.defaults, options);

            if (value === null) {
                options.expires = -1;
            }

            if (typeof options.expires === 'number') {
                var days = options.expires, t = options.expires = new Date();
                t.setDate(t.getDate() + days);
            }

            value = config.json ? JSON.stringify(value) : String(value);

            return (document.cookie = [
                encodeURIComponent(key), '=', config.raw ? value : encodeURIComponent(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path    ? '; path=' + options.path : '',
                options.domain  ? '; domain=' + options.domain : '',
                options.secure  ? '; secure' : ''
            ].join(''));
        }

        // read
        var decode = config.raw ? raw : decoded;
        var cookies = document.cookie.split('; ');
        for (var i = 0, parts; (parts = cookies[i] && cookies[i].split('=')); i++) {
            if (decode(parts.shift()) === key) {
                var cookie = decode(parts.join('='));
                return config.json ? JSON.parse(cookie) : cookie;
            }
        }

        return null;
    };

    config.defaults = {};

    $.removeCookie = function (key, options) {
        if ($.cookie(key) !== null) {
            $.cookie(key, null, options);
            return true;
        }
        return false;
    };

})(jQuery, document);