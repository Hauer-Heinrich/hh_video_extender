// NodeList forEach Implementation
if (window.NodeList && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = Array.prototype.forEach;
}

// Video Defer Loading
window.addEventListener("load", function(e) {
    var vidsContainer = document.querySelectorAll(".video-embed");

    vidsContainer.forEach(function(vidC) {
        var videoPreview = vidC.querySelector(".video-preview");
        if(videoPreview) {
            videoPreview.addEventListener("click", function() {
                videoPreview.classList.add("hide");
            });
        }
    });
});
