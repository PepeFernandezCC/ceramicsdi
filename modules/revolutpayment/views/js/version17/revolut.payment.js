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
  let createCardWidgetInterval = null
  let createCardWidgetRetryCount = 0
  let createPayButtonWidgetInterval = null
  let createPayButtonWidgetRetryCount = null
  let canSubmitRevolutForm = false
  let revolutForm = null
  let revolutCardWidget = null
  let merchant_type = getMerchantType()
  let isRevPayV2 = parseInt($('input#isRevPayV2').val())
  let merchantPublicKey = $('input#merchantPublicKey').val()
  let public_id = $('input#revolutPublicId').val()
  let locale = $('input#revolutLocale').val()
  let cardWidgetIsSubmitting = false
  let termsBox = $('input[name="conditions_to_approve[terms-and-conditions]"]')
  let payment_option_checkbox = $('input[name="payment-option"]')
  let ps_payment_button_wrapper = $('#payment-confirmation')
  let ps_payment_button = $('#payment-confirmation button')
  let rev_pay_v2_instance = null
  let payByBankInstance = null
  const payByBankForm = $('#payByBankOptionForm')
  const PAY_BY_BANK_METHOD_KEY = 'pay_by_bank'
  const payByBankLabel = $("input[data-module-name='Pay by bank']")
    .closest('div.payment-option')
    .find('label')

  updateLogos()

  if (cardWidgetIsEnabled) {
    if (checkoutWidgetDisplayType == 3) {
      // 3 indicates poopup widget
      createCardWidgetInterval = setInterval(initRevolutPop, 500)
    } else {
      createCardWidgetInterval = setInterval(initRevolutCardWidget, 500)
    }
  }

  if (payWidgetIsEnabled) {
    createPayButtonWidgetInterval = setInterval(initRevolutPayButtonWidget, 500)
  }

  function updateLogos() {
    if (typeof logo_path === 'undefined') {
      return
    }

    let visa_logo = `<img class="visa-logo" src="${logo_path}visa-logo.svg"/>`
    let mastercard_logo = `<img class="mastercard-logo"  src="${logo_path}master-card-logo.svg">`
    let amex_logo = `<img class="amex-logo" src="${logo_path}amex.svg">`

    $(`img[src="${logo_path}visa-logo.svg"]`)
      .after(`${visa_logo}${mastercard_logo}`)
      .remove()
    $(`img[src="${revpay_logo}"]`)
      .after(`${visa_logo}${mastercard_logo}`)
      .css({ 'margin-left': '5px', height: '24px', width: '30px' })

    if (amex_availability) {
      $(`img[src="${logo_path}master-card-logo.svg"]`).after(`${amex_logo}`)
    }
  }

  function initRevolutCardWidget() {
    createCardWidgetRetryCount += 1

    if (createCardWidgetRetryCount > 10) {
      clearInterval(createCardWidgetInterval)
    }

    if (!checkWidgetHtmlLoaded()) {
      return false
    }

    clearInterval(createCardWidgetInterval)

    merchant_type = getMerchantType()
    public_id = $('input#revolutPublicId').val()
    locale = $('input#revolutLocale').val()

    RevolutCheckout(public_id, merchant_type).then(function (instance) {
      revolutForm = document.querySelector('#revolutForm')
      revolutCardWidget = instance.createCardField({
        target: document.querySelector('#revolut_card'),
        hidePostcodeField: true,
        locale: locale,
        styles: {
          default: {
            color: '#232323',
            '::placeholder': {
              color: '##7a7a7a',
            },
          },
        },
        onValidation(messages) {
          stopLoading()
          let errorWidget = $('form#revolutForm .error')
          if ($('.revolutPaymentPageErrors').length > 0) {
            errorWidget = $('.revolutPaymentPageErrors')
          }
          revolutDisplayErrors(errorWidget, messages)
        },
        onSuccess() {
          loadingView()
          canSubmitRevolutForm = true
          revolutForm.submit()
        },
        onError(messages) {
          stopLoading()
          let errorWidget = $('form#revolutForm .error')
          if ($('.revolutPaymentPageErrors').length > 0) {
            errorWidget = $('.revolutPaymentPageErrors')
          }
          revolutDisplayErrors(errorWidget, messages)
        },
        onCancel() {
          location.reload()
        },
      })
    })

    $('#revolutForm').on('submit', function (event) {
      cardWidgetIsSubmitting = true
      if (!canSubmitRevolutForm) {
        event.preventDefault()
        event.stopPropagation()
        let customerData = new FormData(revolutForm)
        let customerName = customerData.get('customer_name')

        if (cardholderNameEnabled) {
          const cardholderName = $('input#card-holder-name-field').val()
          customerName = cardholderName
        }

        loadingView()
        revolutCardWidget.submit({
          name: customerName,
          email: customerData.get('email'),
          billingAddress: {
            countryCode: customerData.get('country'),
            region: customerData.get('state'),
            city: customerData.get('city'),
            streetLine1: customerData.get('line1'),
            streetLine2: customerData.get('line2'),
            postcode: customerData.get('postal'),
          },
        })
      }
    })
  }

  $('#revolutPayButton').on('click', function () {
    $('#revolutForm').submit()
  })

  function initRevolutPop() {
    createCardWidgetRetryCount += 1

    if (createCardWidgetRetryCount > 10) {
      clearInterval(createCardWidgetInterval)
    }

    if ($('#revolutForm').length < 1) {
      return false
    }

    clearInterval(createCardWidgetInterval)

    $('#revolutForm').on('submit', function (event) {
      cardWidgetIsSubmitting = true
      if (!canSubmitRevolutForm) {
        event.preventDefault()
        event.stopPropagation()
        merchant_type = getMerchantType()
        public_id = $('input#revolutPublicId').val()
        locale = $('input#revolutLocale').val()
        revolutForm = document.querySelector('form#revolutForm')
        let customerData = new FormData(revolutForm)

        RevolutCheckout(public_id, merchant_type).then(function (instance) {
          instance.payWithPopup({
            locale: locale,
            name: customerData.get('customer_name'),
            email: customerData.get('email'),
            billingAddress: {
              countryCode: customerData.get('country'),
              region: customerData.get('state'),
              city: customerData.get('city'),
              streetLine1: customerData.get('line1'),
              streetLine2: customerData.get('line2'),
              postcode: customerData.get('postal'),
            },
            onSuccess() {
              loadingView()
              canSubmitRevolutForm = true
              revolutForm.submit()
            },
            onError(messages) {
              stopLoading()
              revolutDisplayErrors($('form#revolutForm .error'), messages)
            },
            onCancel() {
              location.reload()
            },
          })
        })
      }
    })
  }

  function initRevolutPayButtonWidget() {
    createPayButtonWidgetRetryCount += 1

    if (createPayButtonWidgetRetryCount > 10) {
      clearInterval(createPayButtonWidgetInterval)
    }

    if ($('#revolut_pay').length < 1) {
      return false
    }

    clearInterval(createPayButtonWidgetInterval)

    merchant_type = getMerchantType()
    public_id = $('input#revolutPublicId').val()
    merchantPublicKey = $('input#merchantPublicKey').val()
    isRevPayV2 = parseInt($('input#isRevPayV2').val())
    locale = $('input#revolutLocale').val()

    if (isRevPayV2) {
      return initRevPayV2()
    }

    RevolutCheckout(public_id, merchant_type).then(function (instance) {
      revolutForm = document.querySelector('form#revolutForm')
      instance.revolutPay({
        target: document.getElementById('revolut_pay'),
        locale: locale,
        phone: $('#phone_number').val(),
        validate: function () {
          if (termsBox.length > 0 && !termsBox.is(':checked')) {
            revolutDisplayErrors(
              $('form#revolutPayForm .error'),
              'Please indicate that you have read and agree to the Terms and Conditions and Privacy Policy',
            )
            return false
          }

          return true
        },
        onCancel: function () {
          location.reload()
        },
        onSuccess() {
          if (!cardWidgetIsSubmitting) {
            loadingView()
            $('#revolutPayForm').submit()
          }
        },
        onError(error) {
          stopLoading()
          revolutDisplayErrors($('form#revolutPayForm .error'), error.message)
        },
        buttonStyle: {
          radius: 'none',
        },
      })
    })
  }

  function initRevPayV2() {
    merchant_type = getMerchantType()
    public_id = $('input#revolutPublicId').val()
    merchantPublicKey = $('input#merchantPublicKey').val()
    isRevPayV2 = parseInt($('input#isRevPayV2').val())
    locale = $('input#revolutLocale').val()
    let orderTotalAmount = parseInt($('input#orderTotalAmount').val())
    let shippingAmount = parseFloat($('input#shippingAmount').val())
    let orderCurrency = $('input#orderCurrency').val()
    let mobileRedirectURL = $('input#mobileRedirectURL').val()
    if (rev_pay_v2_instance !== null) {
      rev_pay_v2_instance.destroy()
    }

    rev_pay_v2_instance = RevolutCheckout.payments({
      locale: locale,
      publicToken: merchantPublicKey,
    })

    const paymentOptions = {
      currency: orderCurrency,
      totalAmount: orderTotalAmount,
      validate: function () {
        if (termsBox.length > 0 && !termsBox.is(':checked')) {
          revolutDisplayErrors(
            $('form#revolutPayForm .error'),
            'Please indicate that you have read and agree to the Terms and Conditions and Privacy Policy',
          )
          return false
        }

        return true
      },
      createOrder: () => {
        return { publicId: public_id }
      },
      deliveryMethods: [
        {
          id: 'id',
          amount: shippingAmount,
          label: 'Shipping',
        },
      ],
      mobileRedirectUrls: {
        success: mobileRedirectURL,
        failure: mobileRedirectURL,
        cancel: mobileRedirectURL,
      },
      __metadata: {
        environment: 'prestashop',
        context: 'checkout',
        origin_url: originUrl,
      },
      buttonStyle: {
        radius: 'none',
        cashbackCurrency: orderCurrency,
      },
    }

    rev_pay_v2_instance.revolutPay.mount(
      document.getElementById('revolut_pay'),
      paymentOptions,
    )

    rev_pay_v2_instance.revolutPay.on('payment', function (event) {
      switch (event.type) {
        case 'success':
          if (!cardWidgetIsSubmitting) {
            loadingView()
            $('#revolutPayForm').submit()
          }
          break
        case 'error':
          stopLoading()
          revolutDisplayErrors(
            $('form#revolutPayForm .error'),
            [event.error.message].filter(Boolean),
          )
          initRevPayV2()
          break
        case 'cancel':
          location.reload()
          break
      }
    })
  }

  termsBox.on('change', function () {
    //empty error view
    revolutDisplayErrors($('form#revolutPayForm .error'), '')
  })

  payment_option_checkbox.on('change', function () {
    if ($(this).parents().eq(2).next().find('#revolutPayForm').length > 0) {
      ps_payment_button_wrapper.addClass('revolut_hide')
    } else {
      ps_payment_button_wrapper.removeClass('revolut_hide')
    }
  })

  function revolutDisplayErrors(errorWdiget, messages) {
    if (errorWdiget.length) {
      errorWdiget.html()
      errorWdiget.addClass('hidden')

      if (messages != '') {
        cardWidgetIsSubmitting = false
        var normalized = [].concat(messages)

        if (normalized.length > 0) {
          var error_html = '<ul>'
          normalized
            .map(function (message) {
              message = message.toString().replace('RevolutCheckout: ', '')
              message = message.toString().replace('Validation: ', '')
              error_html += '<li>' + message + '</li>'
            })
            .join('')
          error_html += '</ul>'

          errorWdiget.html(error_html)
          errorWdiget.removeClass('hidden')
          ps_payment_button.removeClass('disabled')
        }
      }
    }
  }

  function checkWidgetHtmlLoaded() {
    return (
      $('#revolut_card').length &&
      $('#revolutPublicId').length &&
      $('#revolutPublicId').val() !== ''
    )
  }

  function loadingView() {
    $.blockUI({
      message:
        '<svg id="rev-spinner-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="45"/></svg>',
    })
  }

  function stopLoading() {
    $.unblockUI()
  }

  function getMerchantType() {
    let merchant_type = 'prod'
    if ($('#revolutMerchantType').length && $('#revolutMerchantType').val() !== '') {
      merchant_type = $('#revolutMerchantType').val()
    }

    return merchant_type
  }

  function triggerPayByBankWidget() {
    if (payByBankInstance !== null) {
      payByBankInstance.destroy()
    }

    const publicToken = $('input#merchantPublicKey').val()
    const locale = $('input#revolutLocale').val()
    const payByBankInstantPaymentsOnly = Boolean(
      $('input#payByBankInstantPaymentsOnly').val(),
    )

    if (!publicToken) {
      return revolutDisplayErrors(
        $('form#payByBankOptionForm .error'),
        'Something went wrong. Required parameters to initiate the payment were missing',
      )
    }

    let instance = RevolutCheckout.payments({
      locale: locale,
      publicToken: publicToken,
    })

    const paymentOptions = {
      instantOnly: payByBankInstantPaymentsOnly,
      createOrder: () => {
        return $.post(window.create_order_ajax_url, {
          payment_method: PAY_BY_BANK_METHOD_KEY,
        }).then(resp => {
          const json = JSON.parse(resp)
          const publicId = json.public_id

          $('input#payByBankRevolutPublicId').val(publicId)

          return {
            publicId,
          }
        })
      },
      onError: errorMsg => {
        if (errorMsg.error) {
          revolutDisplayErrors($('form#payByBankOptionForm .error'), errorMsg.error)
        } else {
          revolutDisplayErrors($('form#payByBankOptionForm .error'), errorMsg)
        }
        stopLoading()
      },
      onCancel: () => {
        revolutDisplayErrors($('form#payByBankOptionForm .error'), 'Payment cancelled')
        stopLoading()
      },
      onSuccess: () => {
        payByBankForm[0].submit()
      },
    }

    payByBankInstance = instance.payByBank(paymentOptions)

    payByBankInstance.show()
  }

  function showPayByBankLogos() {
    const bankBrandsElement = $('#dataBankBrands')[0]
    const bankBrands = JSON.parse(bankBrandsElement.dataset.bankBrands)

    if (!bankBrands) return

    const { institutions, popular_institution_ids } = bankBrands
    if (!institutions || !popular_institution_ids) {
      return
    }
    const popular = popular_institution_ids
      .map(id =>
        institutions.find(bank => Object.values(bank.details)[0].institution_id === id),
      )
      .filter(Boolean)
    const nonPopular = institutions.filter(
      bank =>
        !popular_institution_ids.includes(Object.values(bank.details)[0].institution_id),
    )

    const bankList = [...popular, ...nonPopular]
    banks_info = {
      firstFive: bankList.slice(0, 5),
      remainingCount: Math.max(0, bankList.length - 5),
    }

    let bank_logos = ''

    banks_info.firstFive.map(bank => {
      bank_logos += `<img src="${bank.logo.value}">`
    })

    payByBankLabel.append(bank_logos)
    const images = payByBankLabel.find('img')
    images.addClass('bankLogo')

    return {
      firstFive: bankList.slice(0, 5),
      remainingCount: Math.max(0, bankList.length - 5),
    }
  }

  if (payByBankForm.length) {
    showPayByBankLogos()
  }

  payByBankForm.on('submit', e => {
    loadingView()
    e.preventDefault()
    e.stopPropagation()
    triggerPayByBankWidget()
  })

  $("span:contains('Revolut Pay')").addClass('notranslate')
})
