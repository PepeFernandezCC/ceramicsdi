/**
 * This file is part of the performancepro package.
 *
 * @author Mathias Reker
 * @copyright Mathias Reker
 * @license Academic Free License (AFL 3.0)
 *
 * Additionally, this module is subject to a proprietary End User License Agreement (EULA).
 * For the full copyright, open source license, and EULA information, please view the LICENSE
 * that were distributed with this source code.
 */

document.addEventListener("DOMContentLoaded", function () {
    const confirmationMessageContainer = document.getElementById("confirmationMessageContainer");
    if (confirmationMessageContainer) {
        const message = confirmationMessageContainer.getAttribute("data-message");
        const type = confirmationMessageContainer.getAttribute("data-type");

        switch (type) {
            case 'error':
                $.growl.error({
                    duration: 120000,
                    title: "",
                    message: message
                });
                break;
            case 'warning':
                $.growl.warning({
                    duration: 20000,
                    title: "",
                    message: message
                });
                break;
            default:
                $.growl.notice({
                    duration: 5000,
                    title: "",
                    message: message
                });
                break;
        }
    }
});
