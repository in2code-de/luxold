define(['jquery', 'TYPO3/CMS/Lux/Vendor/Chart.min'], function($) {
	'use strict';

	/**
	 * Init
	 */
	$(document).ready(function () {
		// Apply DatePicker to all date time fields
		if ($('.t3js-datetimepicker').length) {
			require(['TYPO3/CMS/Backend/DateTimePicker'], function(DateTimePicker) {
				DateTimePicker.initialize();
			});
		}
	})
});
