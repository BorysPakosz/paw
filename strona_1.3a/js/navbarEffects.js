$(document).ready(function() {
    $(".navbar ul li a").hover(
        function() {
            $(this).css({
                transform: "scale(1.2)", 
                transition: "transform 0.3s ease" 
            });
        },
        function() {
            $(this).css({
                transform: "scale(1)", 
                transition: "transform 0.3s ease"
            });
        }
    );
});
