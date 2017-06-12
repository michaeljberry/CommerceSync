<?php
$goal = 0;
?>

</div>
</div>
<div class='modal'>
    <div class='wait'>
        Please wait... <img src='/includes/img/loading29.gif' alt="Please wait"/><br />
        <span id='waitprogress'></span>
    </div>
</div>
<a class="scrollup" title="Scroll to top">Scroll  Up</a>
<script type='text/javascript'>
    $(document).ready(function(){
        var width = $('#main').width();
        var $sticky = $('#sticky');
        var offsat = $sticky.offset();
        var stickyTop = offsat.top;
        var windowTop = $(window).scrollTop();

        function sticky(width){
            windowTop = $(window).scrollTop();
            $sticky.css({position:windowTop>stickyTop ? "fixed" : ""});
            $sticky.css({width:width});
        }
        sticky();
        $(window).scroll(function(e){var width = $('#main').width();sticky(width);});
        $(window).bind('resize', function(e){
            var width = $('#main').width();
            sticky(width);
        });

        $('#nav a').on('click', function(e){
            e.preventDefault();
            var link = $(this).attr('href');
            page = link; //.substring(link.lastIndexOf("/") + 1)
            page = page.toLowerCase();
            page = page.replace(" ", "-");
            if(page == "home" || page == "log-out" || page == "change-password"){
                window.location.replace(page + '.php');
            }else if($(this).hasClass("plugin")){
                console.log(page);
                $('#maincontainer').load('plugin/' + page + '.php');
            }else{
                $('#maincontainer').load(page + '.php');
            }
        });
    });
    var showLoader = true;
    var $body = $('body');
    $.ajaxSetup({global:true, cache: false});
    $(document).ajaxStart(function(){
        if(showLoader){
            $body.addClass("loading");
        }
    });
    $(document).ajaxStop(function(){
        if(showLoader){
            $body.removeClass("loading");
        }
    });
    $(window).scroll(function(){
        if($(this).scrollTop()>100){
            $('.scrollup').fadeIn();
        }else{
            $('.scrollup').fadeOut();
        }
    });
    $('.scrollup').on('click', function(){
        $('html, body').animate({scrollTop: 0}, 600);
        return false;
    });
    function addSubNav(url, menu, page){
        $("#sub-nav").toggle(true);
        $("#sub-nav").load(url,{menu:menu, page:page});
    }
</script>
</body>
</html>