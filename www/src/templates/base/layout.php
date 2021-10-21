<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
	<title>
		{% yield title %}
	</title>
	{% yield style %}
</head>

<body>
	{% yield content %}
	<script src="/assets/js/bootstrap.bundle.min.js"></script>
	{% yield javascript %}
</body>

</html>