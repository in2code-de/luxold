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
			idCookie = getIdCookie();
			setIdCookieIfNotSet();
			pageRequest();
		}
	};

	/**
	 * @returns {void}
	 */
	var pageRequest = function() {
		ajaxConnection(getAjaxUri(), getParametersForAjaxCall());
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
			'tx_lux_fe[languageUid]': getLanguageUid()
		};
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
	var setIdCookieIfNotSet = function() {
		if (idCookie === '') {
			idCookie = getRandomString(32);
			setCookie(cookieName, idCookie);
		}
	};

	/**
	 * @returns {string}
	 */
	var getAjaxUri = function() {
		var container = getContainer();
		if (container !== null) {
			return container.getAttribute('data-lux-ajaxuri');
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
					doAction(jsonObject);
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
