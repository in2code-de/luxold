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
	 * @type {null}
	 */
	this.lightboxInstance = null;

	/**
	 * @type {LuxMain}
	 */
	var that = this;

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
			addEmail4LinkListeners();
			addDownloadListener();
		}
	};

	/**
	 * Close any lightbox
	 */
	this.closeLightbox = function() {
		if (that.lightboxInstance !== null) {
			that.lightboxInstance.close();
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
			ajaxConnection({
				'tx_lux_fe[dispatchAction]': 'pageRequest',
				'tx_lux_fe[idCookie]': getIdCookie(),
				'tx_lux_fe[arguments][pageUid]': getPageUid(),
				'tx_lux_fe[arguments][referrer]': getReferrer()
			}, 'generalWorkflowActionCallback');
		}
	};

	/**
	 * Callback and dispatcher function for all workflow actions
	 *
	 * @params {Json} response
	 * @returns {void}
	 */
	this.generalWorkflowActionCallback = function(response) {
		for (var i = 0; i < response.length; i++) {
			if (response[i]['action']) {
				try {
					that[response[i]['action'] + 'WorkflowAction'](response[i]);
				} catch (error) {
					console.log(error);
				}
			}
		}
	};

	/**
	 * Callback for workflow action "LightboxContent"
	 *
	 * @param response
	 */
	this.lightboxContentWorkflowAction = function(response) {
		var contentElementUid = response['configuration']['contentElement'];
		var uri = document.querySelector('[data-lux-lightboxuri]').getAttribute('data-lux-lightboxuri')
			|| '/index.php?id=5&type=1520192598';
		var html = '<iframe src="' + uri + '&c=' + parseInt(contentElementUid) + '" width="800" height="600"></iframe>';
		that.lightboxInstance = basicLightbox.create(html);
		that.lightboxInstance.show();
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
					fieldListener(this);
				});
			}
		}
	};

	/**
	 * @returns {void}
	 */
	var addEmail4LinkListeners = function() {
		var links = document.querySelectorAll('[data-lux-email4link-title]');
		for (var i = 0; i < links.length; i++) {
			var element = links[i];
			element.addEventListener('click', function(event) {
				email4LinkListener(this, event);
			});
		}
	};

	/**
	 * @returns {void}
	 */
	var addDownloadListener = function() {
		if (isDownloadTrackingEnabled()) {
			var links = document.querySelectorAll(getExpressionForLinkSelection());
			var href;
			for (var i = 0; i < links.length; i++) {
				href = links[i].getAttribute('href');
				links[i].addEventListener('click', function() {
					ajaxConnection({
						'tx_lux_fe[dispatchAction]': 'downloadRequest',
						'tx_lux_fe[idCookie]': getIdCookie(),
						'tx_lux_fe[arguments][href]': this.getAttribute('href')
					}, null);
				});
			}
		}
	};

	/**
	 * @param {Node} link
	 * @param event
	 * @returns {void}
	 */
	var email4LinkListener = function(link, event) {
		event.preventDefault();

		var title = link.getAttribute('data-lux-email4link-title') || '';
		var text = link.getAttribute('data-lux-email4link-text') || '';
		var href = link.getAttribute('href');
		var containers = document.querySelectorAll('[data-lux-container="email4link"]');
		if (containers.length > 0) {
			var container = containers[0].cloneNode(true);
			var html = container.innerHTML;
			html = html.replace('###TITLE###', title);
			html = html.replace('###TEXT###', text);
			html = html.replace('###HREF###', getFilenameFromHref(href));
			that.lightboxInstance = basicLightbox.create(html);
			that.lightboxInstance.element().querySelector('[data-lux-email4link="submit"]').addEventListener('click', function(event) {
				email4LinkLightboxSubmitListener(this, event, link);
			});
			that.lightboxInstance.show();
		}
	};

	/**
	 * Callback function if lightbox should be submitted
	 *
	 * @param {Node} element
	 * @param event
	 * @param {Node} link
	 * @returns {void}
	 */
	var email4LinkLightboxSubmitListener = function(element, event, link) {
		event.preventDefault();
		var href = link.getAttribute('href');
		var sendEmail = link.getAttribute('data-lux-email4link-sendemail') || 'false';
		var email = that.lightboxInstance.element().querySelector('[data-lux-email4link="email"]').value;
		if (validateEmail(email)) {
			addWaitClassToBodyTag();
			ajaxConnection({
				'tx_lux_fe[dispatchAction]': 'email4LinkRequest',
				'tx_lux_fe[idCookie]': getIdCookie(),
				'tx_lux_fe[arguments][email]': email,
				'tx_lux_fe[arguments][sendEmail]': sendEmail === 'true',
				'tx_lux_fe[arguments][href]': href
			}, null);

			if (sendEmail === 'true') {
				hideElement(that.lightboxInstance.element().querySelector('[data-lux-email4link="form"]'));
				showElement(
					that.lightboxInstance.element().querySelector('[data-lux-email4link="successMessageSendEmail"]')
				);
				setTimeout(function() {
					that.lightboxInstance.close();
					removeWaitClassToBodyTag();
				}, 2000);
			} else {
				setTimeout(function() {
					that.lightboxInstance.close();
					window.location = href;
					removeWaitClassToBodyTag();
				}, 500);
			}
		}
	};

	/**
	 * @param {Node} field
	 * @returns {void}
	 */
	var fieldListener = function(field) {
		var key = getKeyOfFieldConfigurationToGivenField(field);
		var value = field.value;
		ajaxConnection({
			'tx_lux_fe[dispatchAction]': 'fieldListeningRequest',
			'tx_lux_fe[idCookie]': getIdCookie(),
			'tx_lux_fe[arguments][key]': key,
			'tx_lux_fe[arguments][value]': value
		}, null);
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
	 * Return an expression for a querySelectorAll function to select all download links
	 * Like 'a[href$="jpg"],a[href$="pdf"]'
	 *
	 * @returns {String}
	 */
	var getExpressionForLinkSelection = function() {
		var extensions = getContainer().getAttribute('data-lux-downloadtracking-extensions').toLowerCase().split(',');
		return 'a[href$="' + extensions.join('"],a[href$="') + '"]';
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
	 * @returns {boolean}
	 */
	var isDownloadTrackingEnabled = function() {
		var enabled = false;
		var container = getContainer();
		if (container !== null) {
			if (container.hasAttribute('data-lux-downloadtracking')) {
				var trackingEnabled = container.getAttribute('data-lux-downloadtracking');
				enabled = trackingEnabled === '1';
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
	var getRequestUri = function() {
		var container = getContainer();
		if (container !== null) {
			return container.getAttribute('data-lux-requesturi');
		}
		return '';
	};

	/**
	 * @params {object} parameters
	 * @returns {void}
	 */
	var ajaxConnection = function(parameters, callback) {
		var uri = getRequestUri();
		if (uri !== '') {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState === 4 && this.status === 200) {
					if (callback !== null) {
						that[callback](JSON.parse(this.responseText));
					}
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
	 * @returns {void}
	 */
	var addWaitClassToBodyTag = function() {
		document.body.className += ' ' + 'lux_waiting';
	};

	/**
	 * @returns {void}
	 */
	var removeWaitClassToBodyTag = function() {
		document.body.classList.remove('lux_waiting');
	};

	/**
	 * @param {Node} element
	 * @returns {void}
	 */
	var hideElement = function(element) {
		element.style.display = 'none';
	};

	/**
	 * @param {Node} element
	 * @returns {void}
	 */
	var showElement = function(element) {
		element.style.display = 'block';
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
	 * Check if string is an email
	 *
	 * @param email
	 * @returns {boolean}
	 */
	var validateEmail = function(email) {
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(email);
	};

	/**
	 * Just show the filename instead of the complete path - but only for asset downloads and not for links to pages
	 * or folders
	 *
	 * @param {String} href
	 * @returns {String}
	 */
	var getFilenameFromHref = function(href) {
		var filename = href.replace(/^.*[\\\/]/, '');
		var fileExtensions = [
			'pdf',
			'txt',
			'doc',
			'docx',
			'xls',
			'xlsx',
			'ppt',
			'pptx',
			'jpg',
			'png',
			'zip'
		];
		if (inArray(getFileExtension(filename).toLowerCase(), fileExtensions)) {
			href = filename;
		}
		return href;
	};

	/**
	 * @param {String} needle
	 * @param {Array} haystack
	 * @returns {boolean}
	 */
	var inArray = function(needle, haystack) {
		var length = haystack.length;
		for (var i = 0; i < length; i++) {
			if (haystack[i] === needle) return true;
		}
		return false;
	};

	/**
	 * @param {String} filename
	 * @returns {String}
	 */
	var getFileExtension = function(filename) {
		if (filename.indexOf('.') !== -1) {
			return filename.split('.').pop();
		}
		return '';
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
