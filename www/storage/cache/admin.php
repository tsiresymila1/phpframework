<?php class_exists('Core\Renderer\Template') or exit; ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
		<title>
			PHPFRAMEWORK
		</title>
			
	</head>

	<body>
		
	<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow p-3 mb-5 rounded-0">
		<div class="container-fluid">
			<a class="navbar-brand" href="#">Navbar w/ text</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarText">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<li class="nav-item">
						<a class="nav-link active" aria-current="page" href="#">Home</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#"><?php echo $name ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#"><?php echo $  ?></a>
					</li>
				</ul>
				<span class="navbar-text">
					Navbar text with an inline element
				</span>
			</div>
		</div>
	</nav>

		<script src="/assets/js/bootstrap.bundle.min.js"></script>
		
	</body>

</html>








