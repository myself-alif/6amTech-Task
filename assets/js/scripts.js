jQuery(document).ready(function ($) {
  toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-center",
    timeOut: "3000",
  };
  $("#sixamtech-contact-form").on("submit", async function (e) {
    e.preventDefault();
    const formData = {
      name: $("input[name='name']").val().trim(),
      email: $("input[name='email']").val().trim(),
      mobile: $("input[name='mobile']").val().trim(),
      address: $("textarea[name='address']").val().trim(),
    };

    try {
      const response = await fetch(API_DATA.url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": API_DATA.nonce,
        },
        body: JSON.stringify(formData),
      });

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const data = await response.json();
      console.log("API Success:", data);

      toastr.success(data.message);
      $("#sixamtech-contact-form")[0].reset();
    } catch (error) {
      console.error("API Request Failed:", error);
      toastr.error("Please type valid information");
    }
  });
});
