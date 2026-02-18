<?php

/** @var yii\web\View $this */

use yii\web\JqueryAsset;
use yii\web\View;

$this->title = 'Short Link Service';
$this->registerJsFile('https://code.jquery.com/jquery-3.6.0.min.js', ['position' => View::POS_HEAD]);
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', ['position' => View::POS_HEAD]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', ['position' => View::POS_HEAD]);
$this->registerJsFile(
	'@web/js/main.js',
	['depends' => [JqueryAsset::class]]
);
?>
<div class="site-index container mt-5">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card shadow">
				<div class="card-body">
					<h2 class="card-title text-center mb-4">Create Short Link</h2>

					<!-- URL Input Form -->
					<form id="shortenForm" class="mb-4">
						<div class="input-group">
							<input
									type="text"
									class="form-control form-control-lg"
									id="urlInput"
									placeholder="Enter full URL (e.g., https://example.com)"
									aria-label="URL"
									required
							>
							<button
									class="btn btn-primary btn-lg"
									type="submit"
									id="shortenBtn"
							>
								OK
							</button>
						</div>
					</form>

					<!-- Result Container (Hidden by default) -->
					<div id="resultContainer" class="d-none">
						<hr>
						<h5 class="mb-3">Your Short Link:</h5>
						<div class="input-group mb-3">
							<input
									type="text"
									class="form-control"
									id="shortLinkOutput"
									readonly
							>
							<button class="btn btn-outline-secondary" type="button" id="copyBtn">
								Copy
							</button>
						</div>

						<div class="text-center">
							<h6>QR Code:</h6>
							<div id="qrCodeContainer" class="mt-2"></div>
						</div>
					</div>

					<!-- Error Message -->
					<div id="errorContainer" class="alert alert-danger d-none mt-3" role="alert"></div>
				</div>
			</div>
		</div>
	</div>
</div>
