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