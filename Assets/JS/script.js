var timer;

$(document).ready(function(){
    
    $(".result").on("click", function(){
        
        var url = $(this).attr("href");
        var id = $(this).attr("data-linkId");

        if(!id){
            alert("Link id not found");
        }
        increaseLinkClicks(id, url);
        
        return false;
    });
    
    var grid = $(".imageResults");
    
    grid.on("layoutComplete", function(){
        $(".gridItem img").css("visibility", "visible");
    });
    
    grid.masonry({
        itemSelector: ".gridItem",
        columnWidth: 200,
        gutter: 5,
        isInitLayout: false
    });
    
    $("[data-fancybox]").fancybox({
        caption : function( instance, item ) {
        var caption = $(this).data('caption') || '';
        var siteUrl = $(this).data('siteurl') || '';
        

        if ( item.type === 'image' ) {
            caption = (caption.length ? caption + '<br />' : '') + 
                '<a href="' + item.src + '">View image</a><br /><a href="' + siteUrl + '">Visit page</a>' ;
        }

        return caption;
    },
        afterShow : function( instance, item ) {
            increaseImageClicks(item.src);
        }
    });
    
});

function increaseLinkClicks(linkId, url){
    
    $.post("Ajax/updateLinkCount.php", {linkId: linkId})
    .done(function(result){
        //ako updateLinkCount.php ispiše nešto - greška
        if(result != ""){  
            alert(result);
            return;
        }
        
        window.location.href = url;
    });
}

function increaseImageClicks(imageUrl){
    
    $.post("Ajax/updateImageCount.php", {imageUrl: imageUrl})
    .done(function(result){
        //ako updateLinkCount.php ispiše nešto - greška
        if(result != ""){  
            alert(result);
            return;
        }
        
    });
}

function loadImage(src, className){
    
    var image = $("<img>");
    
   image.on("load", function(){
        $("." + className + " a").append(image);
       
       clearTimeout(timer);
       
       timer = setTimeout(function(){
           $(".imageResults").masonry();
       }, 500);
       
       
    });
    
    image.on("error", function(){
        $("." + className).remove();
        $.post("Ajax/setBroken.php", {src: src});
    });
    
    image.attr("src", src);
    
}