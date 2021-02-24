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

//$(":input").blur(MarkAsChanged).change(MarkAsChanged);

//$("input[type=button]").click(function(){
//    $(":input:not(.changed)").attr("disabled", "disabled");
//    $("h1").text($("#test").serialize());
//});


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

    myChart.setOption(option);
    myChart.resize();
    
  }


// When document is ready...
//$(document).ready(function() {
//
//    // If cookie is set, scroll to the position saved in the cookie.
//    if ( $.cookie("scroll") !== null ) {
//        $(document).scrollTop( $.cookie("scroll") );
//    }
//
//    // When a button is clicked...
//    $('#test').on("post", function() {
//    
//        // Set a cookie that holds the scroll position.
//        $.cookie("scroll", $(document).scrollTop() );
//    
//    });
//
//});



