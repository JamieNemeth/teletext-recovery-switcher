<?php
	$data = file_exists("data.json") ? json_decode(file_get_contents("data.json"), true) : array();
?>
<!DOCTYPE html>
<html>
	<head>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
		<link href="https://fonts.googleapis.com/css2?family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&amp;display=swap" rel="stylesheet">
		<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
		<style>		
			.row:not(.header-row) {
				min-height: 54px;
			}
		
			.row-bordered {
				background-color: #404040;
				border-top: 1px solid #505050;
			}
			
			body {
				background-color: #0d0d0d;
				font-family: 'Titillium Web', sans-serif;
			}
			
			p {
				margin-bottom: 0.2rem;
			}
		</style>
		
		<script>
			function onSaveDataClick() {
				var username = $("input#username").val();
				var recoveriesFolder = $("input#recoveriesFolder").val();
				
				$.ajax({
					method: "POST",
					url: "save_data.php",
					data: { username: username, recoveriesFolder: recoveriesFolder },
					success: function() { window.location.reload(); }
				});
			}
		
			function onRecoverySwitchClick(runningRecovery) {
				$.ajax({
					method: "POST",
					url: "switch_recovery.php",
					data: { runningRecovery: decodeURIComponent(runningRecovery) }
				});
				
				window.location.reload();
			}
		</script>
	</head>
	<body class="text-white p-5">
		<div class="container-sm">
			<h1>Teletext recovery switcher</h1>
			
			<div class="row p-2 align-items-end">
				<div class="col-4">
					<div class="form-group">
						<label for="username">Username</label>
						<input class="form-control" id="username" type="text" placeholder="pi" value="<?php if (array_key_exists("username", $data)) echo $data["username"]; ?>">
					</div>
				</div>
				<div class="col-4">
					<div class="form-group">
						<label for="recoveriesFolder">Recoveries folder</label>
						<input class="form-control" id="recoveriesFolder" type="text" placeholder="/" value="<?php if (array_key_exists("recoveriesFolder", $data)) echo $data["recoveriesFolder"]; ?>"></input>
					</div>
				</div>
				<div class="col-2">
					<button type="button" class="btn btn-primary" onclick="onSaveDataClick();"><i class="bi bi-floppy"></i> Save</button>
				</div>
			</div>
						
			<div class="row header-row p-2">
				<div class="col-8 align-self-center"><b>Recovery name</b></div>
				<div class="col-4"><b>Switch to service</b></div>
			</div>
			
			<?php
				if (array_key_exists("recoveriesFolder", $data))
				{
					$availableRecoveryUris = glob($data["recoveriesFolder"] . "/*", GLOB_ONLYDIR);
					
					foreach ($availableRecoveryUris as $availableRecoveryUri)
					{
						$ttiFiles = glob($availableRecoveryUri . "/*.tti");
						
						if (count($ttiFiles) > 0)
						{
							
							$availableRecoveryBasenameUri = basename($availableRecoveryUri);
							$runServiceButtonString = (array_key_exists("runningRecovery", $data) && $availableRecoveryBasenameUri == $data["runningRecovery"])
								? '<i class="bi bi-play-fill"></i> Running'
								: '<button type="button" class="btn btn-primary" onclick="onRecoverySwitchClick(\'' . urlencode($availableRecoveryBasenameUri) . '\');"><i class="bi bi-play-fill"></i> Run service</button>';
							
							echo <<<STR
									<div class="row row-bordered p-2 align-items-center">
										<div class="col-8">{$availableRecoveryBasenameUri}</div>
										<div class="col-4">{$runServiceButtonString}</div>
									</div>
								STR;
						}
					}
				}
			?>
		</div>
	</body>
</html>