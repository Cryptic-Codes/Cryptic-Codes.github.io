<div class="row">
    <div class="col-md-12">
		<div class="form-group label-floating is-empty">
			<label class="control-label">Email address</label>
			<input type="email" class="form-control" id="signup_email" required>
            <span class="material-input"></span>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
		<div class="form-group label-floating is-empty" id="signup_password_group">
			<label class="control-label">Password</label>
			<input type="password" class="form-control" id="signup_password" required>
            <span class="material-input"></span>
		</div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
		<div class="form-group label-floating is-empty" id="signup_confrim_password_group">
			<label class="control-label">Confirm Password</label>
			<input type="password" class="form-control" id="signup_confirm_password" required>
            <span class="material-input"></span>
		</div>
    </div>
</div>
<div class="text-center">
    <a id="signup_go" class="btn btn-success btn-round">Signup</a>
</div>
<script>
// Signup Button


// Well i guess if you are reading this then you can make an account, just change
// the boolean below to `true` then you can signup :P
disableSignup = false;
$('#signup_go').on('click', function(event) {
    allowedEmails = [
        "allen.m.lantz@gmail.com"
    ];

    const signup_emailBox = $('#signup_email');
    const signup_passwordBox = $('#signup_password');
    const signup_confirm_passwordBox = $('#signup_confirm_password');
    const signup_usernameBox = $('#signup_username');

    if(BloxAdmin.Auth.loggedIn()) {
        $.notify({
            icon: "info",
            message: 'You are already logged in.'
        },{
            type: 'warning'
        });
        return;
    }

    const email = signup_emailBox.val();
    const password = signup_passwordBox.val();
    const confirm_password = signup_confirm_passwordBox.val();
    const username = signup_usernameBox.val();
    const auth = firebase.auth();
    
    if(!allowedEmails.includes(email) && disableSignup && window.location.search != "?allowSignup") {
        $.notify({
            icon: "error",
            message: "Signup is currently disabled. Sorry."
        
        },{
            type: 'danger',
            timer: 300,
            placement: {
                from: 'bottom',
                align: 'left'
            },
            mouse_over: 'pause'
        });
        
        return;
    }

    if(confirm_password != password) {
        $('#signup_password_group').addClass('has-error')
        $('#signup_confrim_password_group').addClass('has-error')
        $.notify({
            icon: "error",
            message: 'Passwords do not match'
        
        },{
            type: 'danger',
            timer: 300,
            placement: {
                from: 'bottom',
                align: 'left'
            },
            mouse_over: 'pause'
        });
        return;
    }
    
    const promise = auth.createUserWithEmailAndPassword(email, password);

    promise.then(e => {
        BloxAdmin.Page.go(redirect_after_logon);
    });
    promise.catch(e => {
        console.log(e);
        $.notify({
            icon: "error",
            message: e.message
    
        },{
            type: 'danger',
            timer: 300,
            placement: {
                from: 'bottom',
                align: 'left'
            },
            mouse_over: 'pause'
        });
    });
});
</script>