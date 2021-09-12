{% extends 'base.php' %}
{% block content %}
	<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow  mb-5 rounded-0">
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
						<a class="nav-link" href="#">{{ name }}</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#"></a>
					</li>
				</ul>
				<span class="navbar-text">
					{{lower('Navbar text with an inline element')}}
				</span>
			</div>
		</div>
	</nav>
{% endblock %}
