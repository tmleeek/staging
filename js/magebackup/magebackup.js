var MageBackup	= MageBackup || {};

MageBackup.requestTimer		= null;
MageBackup.backupTimer		= null;
MageBackup.ajaxUrl			= null;
MageBackup.errorMsg			= null;

MageBackup.ajaxBackup		= function(url, errorMsg) {
	url			= url || MageBackup.ajaxUrl;
	errorMsg	= errorMsg || MageBackup.errorMsg;

	MageBackup.ajaxUrl	= url;
	MageBackup.errorMsg	= errorMsg;

	MageBackup.$.ajax({
		url:		url,
		data:		'' + new Date().getTime(),
		dataType:	'json',

		success:	function(data) {
			MageBackup.backupResponse(data);
		},

		error:		function() {
			clearInterval(MageBackup.requestTimer);
			clearInterval(MageBackup.backupTimer);

			MageBackup.$('#backup-progress-container').hide(200);
			MageBackup.$('#backup-error-container').show(200);

			MageBackup.$('#backup-error-message').html(errorMsg);
		}
	});
};

MageBackup.backupResponse	= function(data) {
	MageBackup.resetTimer();

	if (data.step != data.nextstep) {
		MageBackup.$('#backup-step-' + data.step).removeClass('backup-step-progress').addClass('backup-step-completed');
		MageBackup.$('#backup-step-' + data.nextstep).addClass('backup-step-progress');
	}

	MageBackup.$('#backup-progress-bar').width(data.progress + '%');
	MageBackup.$('#backup-status').html(data.info);

	if (data.error) {
		clearInterval(MageBackup.requestTimer);
		clearInterval(MageBackup.backupTimer);

		MageBackup.$('#backup-progress-container').hide(200);
		MageBackup.$('#backup-error-container').show(200);

		MageBackup.$('#backup-error-message').html(data.error);
	} else if (data.done) {
		clearInterval(MageBackup.requestTimer);
		clearInterval(MageBackup.backupTimer);

		MageBackup.$('#backup-progress-container').hide(200);
		MageBackup.$('#backup-complete-container').show(200);
	} else {
		MageBackup.ajaxBackup();
	}
};

MageBackup.initTimer	= function() {
	MageBackup.resetTimer();

	var time	= 0;
	MageBackup.backupTimer = setInterval(function() {
		time++;
		MageBackup.$('#backup-time').text(time);
	}, 1000);
};

MageBackup.resetTimer	= function() {
	if (MageBackup.requestTimer) {
		clearInterval(MageBackup.requestTimer);
	}

	var lastResponse	= 0;
	MageBackup.$('#backup-last-response').text(lastResponse);

	MageBackup.requestTimer	= setInterval(function() {
		lastResponse++;
		MageBackup.$('#backup-last-response').text(lastResponse);
	}, 1000);
};


/////////////////////////////////////////////////////////////////

MageBackup.dropboxAuth1	= function(url) {
	window.open(url, 'magebackup_dropbox_request_access_window', 'width=800, height=500');
};

MageBackup.dropboxAuth2	= function(url) {
	var code;

	if (code = prompt('Please enter the code you obtained in step 1')) {
		MageBackup.$.ajax({
			url: url,
			data: {code: code},
			dataType: 'json',
			success: function(data) {
				if (data.error) {
					alert('Error: Could not complete authentication; please retry');
				} else {
					MageBackup.$('#dropbox_access_token').val(data.access_token);
					alert('Authentication success!');
				}
			}
		});
	}
};

/////////////////////////////////////////////////////////////////

MageBackup.googledriveAuth1 = function(url) {
	MageBackup.googledriveWindow = window.open(url, 'magebackup_googledrive_request_access_window', 'width=800, height=500');
};

MageBackup.googledriveAuth2 = function(accessToken, refreshToken) {
	MageBackup.$('#googledrive_access_token').val(accessToken);
	MageBackup.$('#googledrive_refresh_token').val(refreshToken);

	MageBackup.googledriveWindow.close();
};

/////////////////////////////////////////////////////////////////

MageBackup.onedriveAuth1 = function(url) {
	MageBackup.onedriveWindow = window.open(url, 'magebackup_onedrive_request_access_window', 'width=800, height=500');
};

MageBackup.onedriveAuth2 = function(accessToken, refreshToken, redirect_uri) {
	MageBackup.$('#onedrive_access_token').val(accessToken);
	MageBackup.$('#onedrive_refresh_token').val(refreshToken);
	MageBackup.$('#onedrive_redirect_uri').val(redirect_uri);

	MageBackup.onedriveWindow.close();
};