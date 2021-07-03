{% extends base.php %} {% block content %}
<div class="container-fluid">
    <div class="row min-vh-100 justify-content-center" style="background-image: url('/assets/images/fond.jpg');background-repeat:no-repeat; background-size:cover;">
        <div class="col-lg-4 col-md-6 col-sm-8 my-auto">
            <div class="row p-4 ">
                <div class="card rounded-0 border-1">
                    <div class="card-body">
                        <form action="/login" class="form-signin" method="post" accept-charset="utf-8">
                            <div class="form-group mt-2">
                                <div class="justify-content-center" style="text-align:center;">
                                    <img src="http://plateformeevideznia.winkomdesign.com/index.php/../assets/images/logo.png" class="align-self-center mr-3" width="250" alt="...">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="d-flex justify py-2">
                                    <label for="inputEmail" class="text-default">Adresse E-mail </label>
                                </div>
                                <input type="email" name="email" placeholder="exemple@mail.com" style="font-size: 12px; height:auto !important" id="inputEmail" class="form-control rounded-0" required>
                            </div>
                            <div class="form-group">
                                <div class="d-flex justify-content-start py-2">
                                    <label for="inputPassword" class="text-default">Mot de passe</label>
                                </div>
                                <input type="password" placeholder="***********" name="password" style="font-size: 12px;" id="inputPassword" class="form-control rounded-0" required>
                            </div>
                            <div class="form-group my-4">
                                <div class="d-flex justify-content-end ">
                                    <button type="submit" class="pl-4 pr-4 align-left btn btn-primary bg-pink text-white btn-md rounded-0 px-3">Login</button>
                                </div>
                            </div>
                            <div class="d-flex">
                                <a type="button" href="http://plateformeevideznia.winkomdesign.com/index.php/users/reset" class="btn  btn-link">Mot de passe oublié</a>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-6 col-md-6 col-sm-6
                                    align-middle" style="height:50px;">
                                    <img src="http://plateformeevideznia.winkomdesign.com/index.php/../assets/images/masaya1.png" class="align-self-center img-fluid" alt="..." style="max-height:50px;">
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6
                                    align-middle" style="height:50px;">
                                    <img src="http://plateformeevideznia.winkomdesign.com/index.php/../assets/images/baliseken.png" class="align-self-center img-fluid" alt="...">
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{% endblock %}