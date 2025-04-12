jQuery(document).ready(function ($) {
  //toatr global settings
  toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-center",
    timeOut: "3000",
  };

  //add
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
      toastr.success(data.message);
      $("#sixamtech-contact-form")[0].reset();
    } catch (error) {
      toastr.error("Please provide valid information");
    }
  });

  //delete
  $(".delete-contact").on("click", function (e) {
    e.preventDefault();
    const element = $(e.currentTarget);
    toastr.clear();
    const toast = toastr.info(
      `<div>
        Are you sure you want to delete this contact?<br><br>
        <button type="button" class="btn btn-danger btn-sm confirm-delete">Yes</button>
        <button type="button" class="btn btn-secondary btn-sm cancel-delete">No</button>
      </div>`,
      "Confirm Deletion",
      {
        timeOut: 0,
        extendedTimeOut: 0,
        closeButton: true,
        tapToDismiss: false,
        allowHtml: true,
      }
    );
    $(".toast")
      .off("click.confirm")
      .on("click.confirm", ".confirm-delete", async function () {
        try {
          const res = await fetch(`${API_DATA.url}?id=${element.data("id")}`, {
            method: "DELETE",
            headers: {
              "X-WP-Nonce": API_DATA.nonce,
            },
          });
          const data = await res.json();
          $(".toast").fadeOut(200, function () {
            $(this).remove();
          });
          element.closest("tr").slideUp(400, function () {
            $(this).remove();
          });
          toastr.success(data.message || "Contact deleted successfully.");
        } catch (err) {
          toastr.error("Delete failed.");
        }
      });
    $(".toast")
      .off("click.cancel")
      .on("click.cancel", ".cancel-delete", function () {
        $(".toast").fadeOut(200, function () {
          $(this).remove();
        });
      });
  });

  //edit
  async function updateContact(e) {
    const formData = {
      id: parseInt($(e.currentTarget).closest("tr").find(".id").text()),
      name: $(e.currentTarget).closest("tr").find(".name").text(),
      email: $(e.currentTarget).closest("tr").find(".email").text(),
      mobile: $(e.currentTarget).closest("tr").find(".mobile").text(),
      address: $(e.currentTarget).closest("tr").find(".address").text(),
    };
    try {
      const response = await fetch(API_DATA.url, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": API_DATA.nonce,
        },
        body: JSON.stringify(formData),
      });
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      const result = await response.json();
      toastr.success(result.message);
      $(".editable")
        .toArray()
        .forEach((element) => {
          element.style.border = "none";
          $(element).removeAttr("contenteditable");
        });
      $(".edit-contact")
        .toArray()
        .forEach((element) => (element.style.display = "inline-block"));
      $(".data-update")
        .toArray()
        .forEach((element) => {
          element.style.display = "none";
          $(element).off("click", updateContact);
        });
    } catch (error) {
      toastr.error("Invalid information given");
    }
  }

  $(".edit-contact").on("click", function (e) {
    $(".editable")
      .toArray()
      .forEach((element) => {
        element.style.border = "none";
        $(element).removeAttr("contenteditable");
      });
    $(".edit-contact")
      .toArray()
      .forEach((element) => (element.style.display = "inline-block"));
    $(".data-update")
      .toArray()
      .forEach((element) => {
        element.style.display = "none";
        $(element).off("click", updateContact);
      });
    e.currentTarget.style.display = "none";
    $(e.currentTarget).next()[0].style.display = "inline-block";
    $(e.currentTarget).next().on("click", updateContact);
    const cells = $(e.currentTarget).closest("tr").find(".editable").toArray();
    cells.forEach((cell) => {
      cell.style.border = "1px solid black";
      cell.setAttribute("contenteditable", true);
    });
  });
});
