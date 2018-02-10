/**
 * LuxMain functions
 *
 * @class LuxMain
 */
function LuxMain() {
	'use strict';

	/**
	 * @type {string}
	 */
	var cookieName = 'luxId';

	/**
	 * Cookie Id
	 *
	 * @type {string}
	 */
	var idCookie = '';

	/**
	 * Initialize
	 *
	 * @returns {void}
	 */
	this.initialize = function() {
		if (isLuxActivated()) {
			setIdCookie();
			generateNewIdCookieIfNoCookieFound();
			pageRequest();
			addFieldListeners();
		}
	};

	/**
	 * @returns {void}
	 */
	var setIdCookie = function() {
		idCookie = getIdCookie();
	};

	/**
	 * @returns {void}
	 */
	var pageRequest = function() {
		if (isPageTrackingEnabled()) {
			ajaxConnection(getPageRequestUri(), getParametersForAjaxCall());
		}
	};

	/**
	 * @returns {void}
	 */
	var addFieldListeners = function() {
		var elements = document.querySelectorAll('input, textarea, select, radio, check');
		for (var i = 0; i < elements.length; i++) {
			var element = elements[i];
			// Skip every password field and check if this field is configured for listening in TypoScript
			if (element.type !== 'password' && isFieldConfiguredInFieldMapping(element)) {
				element.addEventListener('change', function() {
					listener(this);
				});
			}
		}
	};

	/**
	 * @param {Node} field
	 */
	var listener = function(field) {
		var key = getKeyOfFieldConfigurationToGivenField(field);
		var value = field.value;
		ajaxConnection(
			getFieldListeningRequestUri(),
			{'tx_lux_fe[idCookie]': getIdCookie(), 'tx_lux_fe[key]': key, 'tx_lux_fe[value]': value}
		);
	};

	/**
	 * @param field
	 * @returns {boolean}
	 */
	var isFieldConfiguredInFieldMapping = function(field) {
		return getKeyOfFieldConfigurationToGivenField(field) !== '';
	};

	/**
	 * Pass a field element and check if this field is configured in TypoScript in field mapping. If found get key of
	 * the configuration. Oherwise return an empty string.
	 *
	 * @param field
	 * @returns {string}
	 */
	var getKeyOfFieldConfigurationToGivenField = function(field) {
		var keyConfiguration = '';
		var fieldName = field.name;
		var fieldMapping = getFieldMapping();
		for (var key in fieldMapping) {
			// iterate through fieldtypes
			if (fieldMapping.hasOwnProperty(key)) {
				// iterate through every fieldtype definition
				for (var i = 0; i < fieldMapping[key].length; i++) {
					if (matchStringInString(fieldName, fieldMapping[key][i])) {
						keyConfiguration = key;
					}
				}
			}
		}
		return keyConfiguration;
	};

	/**
	 * Check if string is identically to another string. But if there is a "*", check if the string is part of another
	 * string
	 *
	 * @param haystack
	 * @param needle
	 * @returns {boolean}
	 */
	var matchStringInString = function(haystack, needle) {
		if (needle.indexOf('*') !== -1) {
			needle = needle.replace('*', '');
			var found = haystack.indexOf(needle) !== -1;
		} else {
			found = haystack === needle;
		}
		return found;
	};

	/**
	 * @returns {object}
	 */
	var getFieldMapping = function() {
		var json = {};
		try {
			json = JSON.parse(window.luxFieldMappingConfiguration);
		} catch(err) {
			console.log('Lux: No fieldmapping configuration given.');
		}
		return json;
	};

	/**
	 * Get parameters for ajax call
	 *
	 * @returns {object}
	 */
	var getParametersForAjaxCall = function() {
		return {
			'tx_lux_fe[idCookie]': getIdCookie(),
			'tx_lux_fe[pageUid]': getPageUid(),
			'tx_lux_fe[referrer]': getReferrer(),
			'tx_lux_fe[languageUid]': getLanguageUid()
		};
	};

	/**
	 * @returns {boolean}
	 */
	var isPageTrackingEnabled = function() {
		var enabled = false;
		var container = getContainer();
		if (container !== null) {
			if (container.hasAttribute('data-lux-pagetracking')) {
				var pageTrackingEnabled = container.getAttribute('data-lux-pagetracking');
				enabled = pageTrackingEnabled === '1';
			}
		}
		return enabled;
	};

	/**
	 * @returns {int}
	 */
	var getPageUid = function() {
		var uid = 0;
		var container = getContainer();
		if (container !== null) {
			if (container.hasAttribute('data-lux-pageuid')) {
				var uidContainer = container.getAttribute('data-lux-pageuid');
				uid = parseInt(uidContainer);
			}
		}
		return uid;
	};

	/**
	 * @returns {string}
	 */
	var getReferrer = function() {
		return encodeURIComponent(document.referrer);
	};

	/**
	 * @returns {int}
	 */
	var getLanguageUid = function() {
		var uid = 0;
		var container = getContainer();
		if (container !== null) {
			if (container.hasAttribute('data-lux-languageuid')) {
				var uidContainer = container.getAttribute('data-lux-languageuid');
				uid = parseInt(uidContainer);
			}
		}
		return uid;
	};

	/**
	 * @returns {void}
	 */
	var generateNewIdCookieIfNoCookieFound = function() {
		if (idCookie === '') {
			idCookie = getRandomString(32);
			setCookie(cookieName, idCookie);
		}
	};

	/**
	 * @returns {string}
	 */
	var getPageRequestUri = function() {
		var container = getContainer();
		if (container !== null) {
			return container.getAttribute('data-lux-pagerequesturi');
		}
		return '';
	};

	/**
	 * @returns {string}
	 */
	var getFieldListeningRequestUri = function() {
		var container = getContainer();
		if (container !== null) {
			return container.getAttribute('data-lux-fieldlisteninguri');
		}
		return '';
	};

	/**
	 * @params {string} uri
	 * @params {object} parameters
	 * @returns void
	 */
	var ajaxConnection = function(uri, parameters) {
		if (uri) {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState === 4 && this.status === 200) {
					var jsonObject = JSON.parse(this.responseText);
					// doAction(jsonObject);
				}
			};
			xhttp.open('POST', mergeUriWithParameters(uri, parameters), true);
			xhttp.send();
		} else {
			console.log('No ajax URI given!');
		}
	};

	/**
	 * Build an uri string for an ajax call together with params from an object
	 * 		{
	 * 			'x': 123,
	 * 			'y': 'abc'
	 * 		}
	 *
	 * 		=>
	 *
	 * 		"?x=123&y=abc"
	 *
	 * @params {string} uri
	 * @params {object} parameters
	 * @returns {string} e.g. "index.php?id=123&type=123&x=123&y=abc"
	 */
	var mergeUriWithParameters = function(uri, parameters) {
		for (var key in parameters) {
			if (parameters.hasOwnProperty(key)) {
				if (uri.indexOf('?') !== -1) {
					uri += '&';
				} else {
					uri += '?';
				}
				uri += key + '=' + parameters[key];
			}
		}
		return uri;
	};

	/**
	 * @returns {boolean}
	 */
	var isLuxActivated = function() {
		return getContainer() !== null;
	};

	/**
	 * @returns {object}
	 */
	var getContainer = function() {
		return document.getElementById('lux_container');
	};

	/**
	 * @param {int} length
	 * @returns {string}
	 */
	var getRandomString = function(length) {
		var text = '';
		var possible = 'abcdefghijklmnopqrstuvwxyz0123456789';
		for (var i = 0; i < length; i++) {
			text += possible.charAt(Math.floor(Math.random() * possible.length));
		}
		return text;
	};

	/**
	 * @returns {string}
	 */
	var getIdCookie = function() {
		return getCookieByName(cookieName);
	};

	/**
	 * @param {string} name
	 * @param {string} value
	 * @returns {void}
	 */
	var setCookie = function(name, value) {
		var now = new Date();
		var time = now.getTime();
		time += 3600 * 24 * 365 * 10000; // 10 years from now
		now.setTime(time);
		document.cookie = name + '=' + value + '; expires=' + now.toUTCString() + '; path=/';
	};

	/**
	 * Get cookie value by its name
	 *
	 * @param cookieName
	 * @returns {string}
	 */
	var getCookieByName = function(cookieName) {
		var name = cookieName + '=';
		var ca = document.cookie.split(';');
		for(var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) === ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) === 0) {
				return c.substring(name.length, c.length);
			}
		}
		return '';
	};
}

var Lux = new window.LuxMain();
Lux.initialize();
