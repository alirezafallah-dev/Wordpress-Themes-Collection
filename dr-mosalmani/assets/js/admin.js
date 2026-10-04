jQuery(document).ready(function ($) {
  let frame;

  $("#upload-btn").on("click", function (e) {
    e.preventDefault();
    if (frame) {
      frame.open();
      return;
    }
    frame = wp.media({
      title: "انتخاب تصویر",
      button: { text: "استفاده از تصویر" },
      multiple: false,
    });
    frame.on("select", function () {
      const attachment = frame.state().get("selection").first().toJSON();
      $("#home_image_input").val(attachment.url);
      $(".image-preview").html(
        '<img src="' + attachment.url + '" id="preview-image">',
      );
    });
    frame.open();
  });

  $("#remove-btn").on("click", function (e) {
    e.preventDefault();
    if (!confirm("مطمئنی میخوای تصویر حذف بشه؟")) return;
    $("#home_image_input").val("");
    $(".image-preview").html('<div class="no-image">تصویری انتخاب نشده</div>');

    $.post(themeSettingsConfig.ajaxUrl, {
      action: "save_home_image",
      image: "",
      nonce: themeSettingsConfig.nonce,
    });
  });

  $("#save-btn").on("click", function () {
    const image = $("#home_image_input").val();
    $.post(
      themeSettingsConfig.ajaxUrl,
      {
        action: "save_home_image",
        image: image,
        nonce: themeSettingsConfig.nonce,
      },
      function (res) {
        if (res.success) {
          alert("ذخیره شد ✅");
        } else {
          alert("خطا ❌");
        }
      },
    );
  });
});
