let globalTimer;

$(document).ready(function() {
  // incrementClick click
  $(".result").click(function(e) {
    const id = $(this).attr("data-linkId");
    const url = $(this).attr("href");

    if (!id) {
      alert("data-linkId attribute not found");
    }
    increaseLinkClicks(id, url);
    return false;
  });

  // masonry layout
  const $grid = $(".imageResults");
  $grid.masonry({
    itemSelector: ".gridItem",
    columnWitdh: 200,
    gutter: 5,
    transitionDuration: 0,
    isiInitLayout: false
  });

  $grid.on("layoutComplete", function() {
    $(".gridItem img").css("visibility", "visible");
  });

  // fancybox
  // $(" [data-fancybox]").fancybox();
});

function increaseLinkClicks(linkId, url) {
  $.post("ajax/updateLinkCount.php", {linkId}).done(function(result) {
    if (result != "") {
      alert(result);
      return;
    } else {
      // window.location.href = url;
    }
  });
}

function loadImage(src, className) {
  const $image = $("<img>");
  console.log(className);

  $image.on("load", function() {
    $("." + className + " a").append($image);
    clearTimeout(globalTimer);
    $(".imageResults").masonry;
    globalTimer = setTimeout(function() {
      $(".imageResults").masonry();
    }, 500);
  });
  $image.on("error", function() {
    $("." + className).remove();
    $.post("ajax/setBroken.php", {src});
  });
  $image.attr("src", src);
}
