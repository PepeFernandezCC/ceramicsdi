/*
*
* Google merchant center Pro
*
* @author    BusinessTech.fr - https://www.businesstech.fr
* @copyright Business Tech - https://www.businesstech.fr
* @license   Commercial
*
*           ____    _______
*          |  _ \  |__   __|
*          | |_) |    | |
*          |  _ <     | |
*          | |_) |    | |
*          |____/     |_|
*
*/

// declare the custom label js object
var btHeaderBar = function (sName) {

	/* @param obj iStepId : the id tu update
	* @param int bUpdateWay : the way activate or deactivate
	* @return string : HTML returned by smarty
	* */
	this.updateProgressState = function(iStepId, bUpdateWay) {

		if(bUpdateWay == 'update')
		{
			$('.step-' + iStepId).removeClass('disabled').addClass('complete');
			$('.btn-step-' + iStepId).hide();
		}
		else if (bUpdateWay == 'error') {
			$('.step-' + iStepId).removeClass('complete').addClass('disabled');
			$('.btn-step-' + iStepId).slideUp();
		}
	}
}