$(document).ready(function(){
    $(".dropdown").click(function(){
        $(".dropdown-list ul").toggleClass("active");
    });
    
    $(".dropdown-list ul li").click(function(){
        var icon_text = $(this).html();
        $(".default-option").html(icon_text);
    })

    /* When click outside of search bar, the search bar clears the drop list*/
    $(document).on("click", function(event){
        if(!$(event.target).closest(".dropdown").length){
            $(".dropdown-list ul").removeClass("active")
        }
    });
});