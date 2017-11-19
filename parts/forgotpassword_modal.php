<?php
global $modal1;
?>
<div class="row">
    <div class="col-md-12">
		<div class="form-group label-floating is-empty">
			<label class="control-label">Email address</label>
			<input type="email" class="form-control" id="forgotpass_email" required>
		<span class="material-input"></span></div>
    </div>
</div>
<div class="text-center">
    <a id="forgotpass_go" class="btn btn-success btn-round">Send Reset Email</a>
</div>
<script>
// Signin Button
$('#forgotpass_go').on('click', function() {
    const forgotpass_emailBox = $('#forgotpass_email');

    if(isLoggedIn()) {
        $.notify({
            icon: "info",
            message: 'You are already logged in.'
        },{
            type: 'warning'
        });
        $('#<?php echo $modal1->id; ?>').modal('hide')
        return;
    }

    const email = forgotpass_emailBox.val();

    const promise = firebase.auth().sendPasswordResetEmail(email);

    promise.then(e => {
        $.notify({
            icon: "check",
            message: 'Sent reset email.'
    
        },{});
    });
    promise.catch(e => {
        console.log(e);
        if(e.code == 'auth/user-not-found') {
            message = 'Email was not found or user is banned.';
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
    $('#<?php echo $modal1->id; ?>').modal('hide')
});
</script>