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
  /**
   * Toggle API key sections depending on selected mode
   */
  $(document).on('change', 'select[name="REVOLUT_API_MODE"]', function () {
    var selectedMode = $(this).val()

    // Hide all by default
    $('#REVOLUT_SECRET_API_KEY_LIVE_content').hide()
    $('#REVOLUT_SECRET_API_KEY_SANDBOX_content').hide()
    $('#REVOLUT_SECRET_API_KEY_DEV_content').hide()

    $('#REVOLUT_OAUTH_CONNECTION_SECTION_LIVE').hide()
    $('#REVOLUT_OAUTH_CONNECT_BUTTON_LIVE').hide()
    $('#REVOLUT_OAUTH_CONNECTION_SECTION_DEV').hide()
    $('#REVOLUT_OAUTH_CONNECT_BUTTON_DEV').hide()

    if (selectedMode === 'sandbox') {
      $('#REVOLUT_SECRET_API_KEY_SANDBOX_content').show()
    } else if (selectedMode === 'dev') {
      if ($('#REVOLUT_SECRET_API_KEY_DEV_INPUT').val()) {
        $('#REVOLUT_SECRET_API_KEY_DEV_content').show()
      } else {
        $('#REVOLUT_OAUTH_CONNECTION_SECTION_DEV').show()
        $('#REVOLUT_OAUTH_CONNECT_BUTTON_DEV').show()
      }
    } else {
      // live
      if ($('#REVOLUT_SECRET_API_KEY_LIVE_INPUT').val()) {
        $('#REVOLUT_SECRET_API_KEY_LIVE_content').show()
      } else {
        $('#REVOLUT_OAUTH_CONNECTION_SECTION_LIVE').show()
        $('#REVOLUT_OAUTH_CONNECT_BUTTON_LIVE').show()
      }
    }
  })

  /**
   * Toggle custom status section
   */
  $(document).on('click', 'input[name="REVOLUT_P_CUSTOM_STATUS"]', function () {
    var value = $('input[name="REVOLUT_P_CUSTOM_STATUS"]:checked').val()
    if (value === '1') {
      $('#REVOLUT_P_CUSTOM_STATUS_content').slideDown()
    } else {
      $('#REVOLUT_P_CUSTOM_STATUS_content').slideUp()
    }
  })

  /**
   * Toggle webhook URLs visibility
   */
  $(document).on('click', '#showSandboxWebhookUrls', function () {
    $('#sandboxWebhookUrls').slideDown()
    $(this).hide()
    $('#hideSandboxWebhookUrls').show()
  })
  $(document).on('click', '#hideSandboxWebhookUrls', function () {
    $('#sandboxWebhookUrls').slideUp()
    $(this).hide()
    $('#showSandboxWebhookUrls').show()
  })
  $(document).on('click', '#showLiveWebhookUrls', function () {
    $('#liveWebhookUrls').slideDown()
    $(this).hide()
    $('#hideLiveWebhookUrls').show()
  })
  $(document).on('click', '#hideLiveWebhookUrls', function () {
    $('#liveWebhookUrls').slideUp()
    $(this).hide()
    $('#showLiveWebhookUrls').show()
  })

  /**
   * Setup PRB locations with Select2
   */
  $('#REVOLUT_PRB_LOCATIONS')
    .attr('name', 'REVOLUT_PRB_LOCATIONS[]')
    .attr('multiple', 'multiple')

  let REVOLUT_PRB_SELECTED_LOCATIONS = $('.REVOLUT_PRB_SELECTED_LOCATIONS').val()
  if (REVOLUT_PRB_SELECTED_LOCATIONS) {
    $('#REVOLUT_PRB_LOCATIONS').val(JSON.parse(REVOLUT_PRB_SELECTED_LOCATIONS))
    $('#REVOLUT_PRB_LOCATIONS').select2()
  }

  /**
   * Switch from API key input to OAuth
   */
  $("[id^='REVOLUT_SECRET_API_KEY_'][id$='_content']").on(
    'click',
    'a#revolut-switch-to-oauth',
    function (e) {
      e.preventDefault()
      var selectedMode = $('select[name="REVOLUT_API_MODE"]').val()

      if (selectedMode === 'live') {
        $('#REVOLUT_SECRET_API_KEY_LIVE_content').hide()
        $('#REVOLUT_OAUTH_CONNECTION_SECTION_LIVE').show()
        $('#REVOLUT_OAUTH_CONNECT_BUTTON_LIVE').show()
      }

      if (selectedMode === 'dev') {
        $('#REVOLUT_SECRET_API_KEY_DEV_content').hide()
        $('#REVOLUT_OAUTH_CONNECTION_SECTION_DEV').show()
        $('#REVOLUT_OAUTH_CONNECT_BUTTON_DEV').show()
      }
    },
  )

  /**
   * Switch from OAuth back to API key
   */
  $("[id^='REVOLUT_OAUTH_CONNECT_BUTTON_']").on(
    'click',
    'a.revolut-switch-to-api',
    function (e) {
      e.preventDefault()

      var selectedMode = $('select[name="REVOLUT_API_MODE"]').val()

      if (selectedMode === 'live') {
        $('#REVOLUT_OAUTH_CONNECTION_SECTION_LIVE').hide()
        $('#REVOLUT_SECRET_API_KEY_LIVE_content').show()
      }

      if (selectedMode === 'dev') {
        $('#REVOLUT_OAUTH_CONNECTION_SECTION_DEV').hide()
        $('#REVOLUT_SECRET_API_KEY_DEV_content').show()
      }
    },
  )

  $('select[name="REVOLUT_API_MODE"]').trigger('change')
})
