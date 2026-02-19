$(document).ready(function () {
	$('#shortenForm').on('submit', function (e) {
		e.preventDefault();
		const url = $('#urlInput').val().trim();
		
		// Clear previous results
		$('#resultContainer').addClass('d-none');
		$('#errorContainer').addClass('d-none').text('');
		
		if (!url) {
			$('#errorContainer')
				.text('Please enter a valid URL.')
				.removeClass('d-none');
			return;
		}
		
		// Disable button and show loading
		const $btn = $('#shortenBtn');
		$btn.prop('disabled', true).text('Processing...');
		
		$.ajax({
			url: '/api/link',
			type: 'POST',
			data: {host: url},
			dataType: 'json',
			success: function (response) {
				if (response.host) {
					$('#shortLinkButton')
						.attr('href', '/api/link/visit?short='+ response.short)
						.text('https://' + response.short)
						.removeClass('d-none');
					$('#qrCodeContainer').html(`<img src="${response.qr_code}" alt="QR Code" style="max-width: 200px;">`);
					$('#resultContainer').removeClass('d-none');
				} else {
					$('#errorContainer')
						.text(response.message || 'An error occurred.')
						.removeClass('d-none');
				}
			},
			error: function (response) {
				let msg = '';
				
				if (typeof response.responseJSON == 'object' && (response.responseJSON).length) {
					msg = (response.responseJSON).map(function (message) {
						return message.message
					})
				} else {
					msg = response.responseJSON.message;
				}
				
				if (typeof msg === "object"
					&& (
						(msg.toString() === 'Original URL "' + url + '" has already been taken.') ||
						(msg.toString() === 'Original URL "https://' + url + '" has already been taken.')
					)
				) {
					let host = url.substring(0, 8);
					
					$.ajax({
						url: '/api/link/search?host=' + host,
						type: 'GET',
						data: {host: url},
						dataType: 'json',
						success: function (response) {
							if (response !== null && response.host) {
								$('#shortLinkButton')
									.attr('href', response.host)
									.text('https://' + response.short)
									.removeClass('d-none');
								$('#qrCodeContainer').html(`<img src="${response.qr_code}" alt="QR Code" style="max-width: 200px;">`);
								$('#resultContainer').removeClass('d-none');
							} else if (response !== null && response.message) {
								
								$('#errorContainer')
									.text(response.message || 'An error occurred.')
									.removeClass('d-none');
							}
						},
					});
				}
				
				$('#errorContainer')
					.text(msg)
					.removeClass('d-none');
			},
			complete: function () {
				// Re-enable button
				$btn.prop('disabled', false).text('OK');
			}
		});
	});
});
