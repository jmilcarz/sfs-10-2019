<div id="body-container">
    <div class="container">
      <div class="row justify-content-center">
         <div class="col-md-8 col-lg-5">
            <h1>Register</h1>
            <p>if you already have an account, <a href="/login" data-link="/login">login</a>!</p>
            <form id="register-form">
               <div class="alert alert-danger d-none" id="register-form-errors" role="alert"></div>
               <input type="hidden" name="token" id="registerFormToken" value="<?php echo $_SESSION['LoginToken']; ?>">
               <div class="form-group row">
                    <div class="col-sm-6">
                        <input type="text" placeholder="First Name" class="form-control" id="registerFormFname">
                    </div>
                    <div class="col-sm-6">
                        <input type="text" placeholder="Last Name" class="form-control" id="registerFormLname">
                    </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <input type="text" placeholder="Username" class="form-control" id="registerFormUsername">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <input type="email" placeholder="Email" class="form-control" id="registerFormEmail">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <input type="password" placeholder="Password" class="form-control" id="registerFormPassword">
                        </div>
                        <div class="col-sm-6">
                            <input type="password" placeholder="Repeat Password" class="form-control" id="registerFormPasswordRepeat">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <select id="registerFormGender" class="form-control">
                                <option value="-">Gender</option>
                                <option value="f">Female</option>
                                <option value="m">Male</option>
                                <option value="o">Other</option>
                                <option value="i">I prefer not to say</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <input type="submit" class="form-control btn btn-warning" value="Continue">
                        </div>
                    </div>
            </form>
         </div>
      </div>
   </div>

    <script>
        $("#register-form").on('submit', function(e) {
            e.preventDefault();
            
            let fname = $("#registerFormFname").val();
            let lname = $("#registerFormLname").val();
            let username = $("#registerFormUsername").val();
            let email = $("#registerFormEmail").val();
            let password = $("#registerFormPassword").val();
            let passwordRepeat = $("#registerFormPasswordRepeat").val();
            let gender = $("#registerFormGender").val();
            let token = $("#registerFormToken").val();

            $.ajax({
                url: "/fb/app/ajax/auth.php?t=register",
                type: 'POST',
                data: { fname: fname, lname: lname, username: username, email: email, password: password, passwordRepeat: passwordRepeat, gender: gender, token: token },
                success: function(data) {
                    console.log(data);
                    data = JSON.parse(data)
                    let url = window.location.href.substring(24);

                    if (data.type == "error") {
                        $("#register-form-errors").removeClass("d-none").html(data.m);
                    } else if (data.type == "success") {
                        $("#register-form-errors").removeClass("d-none alert-danger").addClass("alert-success").html(data.m);
                        reloadHomePage(url);
                    }
                    refreshLinks(url);
                }
            })
        });
        $(function() {
            setTitle("/Register", ['SFS']);
        });
    </script>
</div>