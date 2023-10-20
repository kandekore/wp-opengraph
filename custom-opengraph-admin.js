jQuery(document).ready(function ($) {
  $("#custom_opengraph_image_button").click(function (e) {
    e.preventDefault();
    var customUploader = wp.media({
      title: "Select OpenGraph Image",
      button: {
        text: "Select"
      },
      multiple: false
    });

    customUploader.on("select", function () {
      var attachment = customUploader.state().get("selection").first().toJSON();
      $("#custom_opengraph_image").val(attachment.url);
    });

    customUploader.open();
  });
});
