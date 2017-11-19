<?php
global $modal1Id;
?>
<form id="signin_form">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group label-floating is-empty">
                <label class="control-label">Email address</label>
                <input type="email" class="form-control" id="signin_email">
            <span class="material-input"></span></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group label-floating is-empty">
                <label class="control-label">Password</label>
                <input type="password" class="form-control" id="signin_password">
                <a href="#"><span class="material-input ignore-link" data-toggle="modal" data-target="#<?php echo $modal1Id; ?>">Forgot Password?</span></a>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" id="signin_go" class="btn btn-success btn-round">Login</button>
    </div>
</form>
<script>
// Signin Button
$('#signin_form').on('submit', function(event) {
    event.preventDefault();
    const signin_emailBox = $('#signin_email');
    const signin_passwordBox = $('#signin_password');

    if(BloxAdmin.Auth.loggedIn()) {
        $.notify({
            icon: "info",
            message: 'You are already logged in.'
        },{
            type: 'warning'
        });
        return;
    }

    const email = signin_emailBox.val();
    const password = signin_passwordBox.val();
    const auth = firebase.auth();

    const promise = auth.signInWithEmailAndPassword(email, password);

    promise.then(e => {
        BloxAdmin.Page.go(redirect_after_logon);
    });
    promise.catch(e => {
        if(e.code == "auth/user-not-found") {
            message = 'The email or password you entered is incorrect.';
        } else if(e.code == "auth/wrong-password") {
            message = 'The email or password you entered is incorrect. <a style="text-style: underline" href="/account/forgot?email='+encodeURIComponent(email)+'"><u>Forgot Password?</u></a>';
        } else {
            message = e.message;
        }
        $.notify({
            icon: "error",
            message: message
    
        },{
            type: 'danger'
        });
    });
});
</script>