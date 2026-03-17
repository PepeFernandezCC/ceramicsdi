/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Revolut
 * @copyright Since 2020 Revolut
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 */

$(document).ready(function () {
  let cardWidgetIsSubmitting = false;
  let rev_pay_v2_instance = null;
  let payByBankInstance = null;
  const payByBankForm = $("#payByBankOptionForm");
  const PAY_BY_BANK_METHOD_KEY = "pay_by_bank";

  generateCardField();

  $(document).on("click", "#cgv", function () {
    if ($("#cgv:checked").length !== 0) {
      $(document).ajaxComplete(function () {
        generateCardField();
      });
    }
  });

  $(document).on("click", "#revolutPaymentButton", function (e) {
    e.preventDefault();
    initRevolutPop();
  });

  $(document).on("click", "#revolutPayByBankV16Button", function (e) {
    loadingView();
    e.preventDefault();
    triggerPayByBankWidget();
  });

  function loadingView() {
    $.blockUI({
      message:
        '<svg id="rev-spinner-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="45"/></svg>',
    });
  }

  function stopLoading() {
    $.unblockUI();
  }

  function generateCardField() {
    var merchant_type = "prod";
    if (
      $("#revolutMerchantType").length &&
      $("#revolutMerchantType").val() !== ""
    ) {
      merchant_type = $("#revolutMerchantType").val();
    }
    var public_id = $("#revolutPublicId").val();
    let isRevPayV2 = parseInt($("input#isRevPayV2").val());
    if (
      $("#revolut_card").length &&
      $("#revolutPublicId").length &&
      $("#revolutPublicId").val() !== ""
    ) {
      RevolutCheckout(public_id, merchant_type).then(function (instance) {
        var form = document.querySelector("form#revolutForm");
        var card = instance.createCardField({
          target: document.querySelector("#revolut_card"),
          hidePostcodeField: true,
          locale: revolut_locale,
          styles: {
            default: {
              color: "#232323",
              "::placeholder": {
                color: "##7a7a7a",
              },
            },
          },
          onValidation(messages) {
            revolutDisplayErrors($("#revolutForm .error"), messages);
          },
          onSuccess() {
            loadingView();
            form.submit();
          },
          onError(messages) {
            cardWidgetIsSubmitting = false;
            revolutDisplayErrors($("#revolutForm .error"), messages);
          },
          onCancel() {
            location.reload();
          },
        });

        $("#cart_navigation, #payment-confirmation").on(
          "click",
          "button",
          function (event) {
            event.preventDefault();
            event.stopPropagation();
            cardWidgetIsSubmitting = true;

            var data = new FormData(form);
            card.submit({
              name: data.get("customer_name"),
              email: data.get("email"),
              billingAddress: {
                countryCode: data.get("country"),
                region: data.get("state"),
                city: data.get("city"),
                streetLine1: data.get("line1"),
                streetLine2: data.get("line2"),
                postcode: data.get("postal"),
              },
            });
          }
        );
      });
    }

    if (checkWidgetHtmlLoaded()) {
      if (isRevPayV2) {
        return initRevPayV2();
      }

      RevolutCheckout(public_id, merchant_type).then(function (instance) {
        instance.revolutPay({
          target: document.getElementById("revolut_pay"),
          locale: revolut_locale,
          phone: $("#phone_number").val(),
          validate: function () {
            return true;
          },
          onCancel: function () {
            location.reload();
          },
          onSuccess() {
            if (!cardWidgetIsSubmitting) {
              loadingView();
              $("#revolutPayForm").submit();
            }
          },
          onError(error) {
            revolutDisplayErrors($("#revolutPayForm .error"), error.message);
          },
          buttonStyle: {
            radius: "none",
          },
        });
      });
    }
  }

  function initRevPayV2() {
    let public_id = $("input#revolutPublicId").val();
    let merchantPublicKey = $("input#merchantPublicKey").val();
    let locale = $("input#revolutLocale").val();
    let orderTotalAmount = parseInt($("input#orderTotalAmount").val());
    let shippingAmount = parseFloat($("input#shippingAmount").val());
    let orderCurrency = $("input#orderCurrency").val();
    let mobileRedirectURL = $("input#mobileRedirectURL").val();

    if (rev_pay_v2_instance !== null) {
      rev_pay_v2_instance.destroy();
    }

    rev_pay_v2_instance = RevolutCheckout.payments({
      locale: locale,
      publicToken: merchantPublicKey,
    });

    const paymentOptions = {
      currency: orderCurrency,
      totalAmount: orderTotalAmount,
      createOrder: () => {
        return { publicId: public_id };
      },
      deliveryMethods: [
        {
          id: "id",
          amount: shippingAmount,
          label: "Shipping",
        },
      ],
      mobileRedirectUrls: {
        success: mobileRedirectURL,
        failure: mobileRedirectURL,
        cancel: mobileRedirectURL,
      },
      __metadata: {
        environment: "prestashop-v1.6",
        context: "checkout",
        origin_url: originUrl,
      },
      buttonStyle: {
        height: 50,
        radius: "none",
        cashbackCurrency: orderCurrency,
      },
    };

    rev_pay_v2_instance.revolutPay.mount(
      document.getElementById("revolut_pay"),
      paymentOptions
    );

    rev_pay_v2_instance.revolutPay.on("payment", function (event) {
      switch (event.type) {
        case "success":
          if (!cardWidgetIsSubmitting) {
            loadingView();
            $("#revolutPayForm").submit();
          }
          break;
        case "error":
          revolutDisplayErrors(
            $("#revolutPayForm .error"),
            [event.error.message].filter(Boolean)
          );
          initRevPayV2();
          break;
        case "cancel":
          location.reload();
          break;
      }
    });
  }

  function initRevolutPop() {
    var form = document.querySelector("form#revolutForm");

    if ($("#revolutForm").hasClass("payment_page")) {
      $("#revolutForm").submit();
      return;
    }

    cardWidgetIsSubmitting = true;
    var merchant_type = "prod";
    if (
      $("#revolutMerchantType").length &&
      $("#revolutMerchantType").val() !== ""
    ) {
      merchant_type = $("#revolutMerchantType").val();
    }
    var public_id = $("#revolutPublicId").val();
    var data = new FormData(form);
    var locale = $("input#revolutLocale").val();

    RevolutCheckout(public_id, merchant_type).then(function (instance) {
      instance.payWithPopup({
        locale: locale,
        name: data.get("customer_name"),
        email: data.get("email"),
        billingAddress: {
          countryCode: data.get("country"),
          region: data.get("state"),
          city: data.get("city"),
          streetLine1: data.get("line1"),
          streetLine2: data.get("line2"),
          postcode: data.get("postal"),
        },
        onSuccess() {
          loadingView();
          form.submit();
        },
        onError(messages) {
          cardWidgetIsSubmitting = false;
          revolutDisplayErrors($("form#revolutForm .error"), messages);
        },
        onCancel() {
          location.reload();
        },
      });
    });
  }

  function revolutDisplayErrors(errorWdiget, messages) {
    if (errorWdiget.length) {
      errorWdiget.html();
      errorWdiget.addClass("hidden");

      if (messages != "") {
        var normalized = [].concat(messages);

        if (normalized.length > 0) {
          var error_html = "<ul>";
          normalized
            .map(function (message) {
              message = message.toString().replace("RevolutCheckout: ", "");
              message = message.toString().replace("Validation: ", "");
              error_html += "<li>" + message + "</li>";
            })
            .join("");
          error_html += "</ul>";

          errorWdiget.html(error_html);
          errorWdiget.removeClass("hidden");
        }
      }
    }
  }

  function checkWidgetHtmlLoaded() {
    return (
      $("#revolut_pay").length > 0 &&
      $("#revolutPublicId").length &&
      $("#revolutPublicId").val() !== ""
    );
  }

  function loadingView() {
    $.blockUI({
      message:
        '<svg id="rev-spinner-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="45"/></svg>',
    });
  }

  function triggerPayByBankWidget() {
    if (payByBankInstance !== null) {
      payByBankInstance.destroy();
    }

    const publicToken = $("input#merchantPublicKey").val();
    const locale = $("input#revolutLocale").val();
    const payByBankInstantPaymentsOnly = Boolean(
      $("input#payByBankInstantPaymentsOnly").val()
    );

    if (!publicToken) {
      return revolutDisplayErrors(
        $("form#payByBankOptionForm .error"),
        "Something went wrong. Required parameters to initiate the payment were missing"
      );
    }

    let instance = RevolutCheckout.payments({
      locale: locale,
      publicToken: publicToken,
    });

    const paymentOptions = {
      instantOnly: payByBankInstantPaymentsOnly,
      createOrder: () => {
        return $.post(window.create_order_ajax_url, {
          payment_method: PAY_BY_BANK_METHOD_KEY,
        }).then((resp) => {
          const json = JSON.parse(resp);
          const publicId = json.public_id;

          $("input#payByBankRevolutPublicId").val(publicId);

          return {
            publicId,
          };
        });
      },
      onError: (errorMsg) => {
        if (errorMsg.error) {
          revolutDisplayErrors(
            $("form#payByBankOptionForm .error"),
            errorMsg.error
          );
        } else {
          revolutDisplayErrors($("form#payByBankOptionForm .error"), errorMsg);
        }

        stopLoading();
      },
      onCancel: () => {
        revolutDisplayErrors(
          $("form#payByBankOptionForm .error"),
          "Payment cancelled"
        );
        stopLoading();
      },
      onSuccess: () => {
        payByBankForm[0].submit();
      },
    };

    payByBankInstance = instance.payByBank(paymentOptions);
    payByBankInstance.show();
  }

  function showPayByBankLogos() {
    const bankBrandsElement = $("#dataBankBrands")[0];
    const bankBrands = JSON.parse(bankBrandsElement.dataset.bankBrands);

    if (!bankBrands) return;

    const { institutions, popular_institution_ids } = bankBrands;
    if (!institutions || !popular_institution_ids) {
      return;
    }
    const popular = popular_institution_ids
      .map((id) =>
        institutions.find(
          (bank) => Object.values(bank.details)[0].institution_id === id
        )
      )
      .filter(Boolean);
    const nonPopular = institutions.filter(
      (bank) =>
        !popular_institution_ids.includes(
          Object.values(bank.details)[0].institution_id
        )
    );
    
    const bankList = [...popular, ...nonPopular];
    banks_info = {
      firstFive: bankList.slice(0, 5),
      remainingCount: Math.max(0, bankList.length - 5),
    };

    let bank_logos = "";

    banks_info.firstFive.map((bank) => {
      bank_logos += `<img src="${bank.logo.value}">`;
    });

    $("#payByBankContainer").append(bank_logos);

    return {
      firstFive: bankList.slice(0, 5),
      remainingCount: Math.max(0, bankList.length - 5),
    };
  }

  if (payByBankForm.length) {
    showPayByBankLogos();
  }

  $("span:contains('Revolut Pay')").addClass("notranslate");
});
