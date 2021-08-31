/**
 * Created by shaimaa on 2/1/2018.
 */
$(document).ready(function() {

    /*--------------Admin------------------------*/
    $(".admin-change ul li").hide();
    $(".admin-change ul li.activeA").show();
    /*next button*/
    $(".admin-buttons .next ").click(function(e){
        e.preventDefault();
        $(".steps .step-num.active").next().addClass("active");
        var a=$(".admin-change ul li.activeA").next();
        $(".admin-change ul li").hide().removeClass("activeA");
        $(a).show().addClass("activeA");
    });
    /*back button*/
    $(".admin-buttons .back ").click(function(e){
        e.preventDefault();
        $(".steps .step-num.active").last().removeClass("active");
        var b=$(".admin-change ul li.activeA").prev();
        $(".admin-change ul li").hide().removeClass("activeA");
        $(b).show().addClass("activeA");
    });
    /*------------Dashboard------------------------*/
    $(".menu-click").click(function () {
        $(this).toggleClass("activate").next().toggle();
    });

    /*left first load*/
    $(".add .relateDiv").hide();
    $(".add .relateDiv#addA").show();

    /*when click on diffrent right link */
    /*click Function*/
    $(".relate a").click(function(e){
        /*prevent a:href(#) default behaviour*/
        e.preventDefault();
        /*right Style*/
        $(".relate a").removeClass("active");
        $(this).addClass("active");
        /*left*/
        var x=$(this).attr("href");
        $(".add .relateDiv").hide();
        var y=".add .relateDiv" + x;
        $(y).show();
    });

  
 
 
	

});