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
		idCookie = getIdCookie();
		setIdCookieIfNotSet();
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
		time += 3600 * 24 * 365 * 5000; // 5 years from now
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
