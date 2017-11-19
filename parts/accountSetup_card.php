<?php
if($user['roblox'] != null) { 
?>
<div class="text-center">
    <img src="https://www.roblox.com/headshot-thumbnail/image?userId=<?php echo $user['roblox']; ?>&width=150&height=150&format=png" class="img-circle img-thumbnail" style="width: 150px; height: 150px;">
</div>
<?php
}
?>
<h2 class="text-center">Welcome to BloxAdmin!</h2>
<h3 class="text-center">You're almost done setting up your account</h3>
<hr/>
<div class="alert alert-info">
    <div class="container-fluid">
    <div class="alert-icon">
        <i class="material-icons">info_outline</i>
    </div>
    <b>Info:</b> BloxAdmin is still in alpha and many featudes may not work. Also these featudes could be changed or removed in the future.
    </div>
</div>
<br/>
<h3 class="text-center">
   Now that you made your account follow these steps to get started with your first place
</h3>
<div class="row">
    <div class="col-sm-12 col-md-8 col-md-offset-2">
		<div class="card card-stats" id="card_step1">
			<div class="card-header" data-background-color="red">
				<i class="material-icons">check_box_outline_blank</i>
			</div>
			<div class="card-content">
				<h3 class="title">Create your account.</h3>
                <hr/>
				<p class="category text-left">Create your account on the home page and login.</p>
                <button class="btn btn-success btn-round" onclick="check_step1()"  id="verify_step1">Verify</button>
			</div>
		</div>
	</div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-8 col-md-offset-2">
		<div class="card card-stats" id="card_step2">
			<div class="card-header" data-background-color="red">
				<i class="material-icons">check_box_outline_blank</i>
			</div>
			<div class="card-content">
				<h3 class="title">Verify your roblox account</h3>
                <hr/>
				<p class="category text-left">
                    Go to <a href="https://www.roblox.com/games/1014425516/Verify-Account-for-BloxAdmin" target="_blank">Verify Account for BloxAdmin</a> on roblox and press <b>Play</b>. After the game is loaded enter your email, <code id="pre_uid" class="text-primary">Loading email...</code>, then press <b>Verify</b> and close Roblox.
                </p>
                <button class="btn btn-success btn-round" onclick="check_step2()" id="verify_step2">Verify</button>
			</div>
		</div>
	</div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-8 col-md-offset-2">
		<div class="card card-stats" id="card_step3">
			<div class="card-header" data-background-color="red">
				<i class="material-icons">check_box_outline_blank</i>
			</div>
			<div class="card-content">
				<h3 class="title">Add scripts to your place</h3>
                <hr/>
				<p class="category text-left">Add <a href="https://www.roblox.com/catalog/1050968974/redirect" target="_blank">this</a> model to your place and follow the instuctions inside of it.</p>
                <button class="btn btn-success btn-round" onclick="check_step3()" id="verify_step3">Verify</button>
			</div>
		</div>
	</div>
</div>
<script>
s1Complete = false;
s2Complete = false;
s3Complete = false;

function gratz(r, a) {
    if(a == null) {
        if(r) {
            $.notify({
                icon: "check",
                message: 'You completed another step!'
            
            },{
                type: 'success'
            });
        } else {
            $.notify({
                icon: "close",
                message: 'Sorry. You didn\'t complete that step.'
            
            },{
                type: 'warning'
            });
        }
    }
    if(s1Complete && s2Complete && s3Complete) {
        BloxAdmin.Page.go('/dashboard')
    }
}

function check_step1(a) {
    loggedIn = BloxAdmin.Auth.loggedIn();
    if(loggedIn) {
        $('#card_step1>.card-header').attr('data-background-color', 'green');
        $('#card_step1>.card-header>.material-icons').html('check_box');
        $('#card_step1>.card-content>button').addClass('hide');
        s1Complete = true;
    }
    
    gratz(loggedIn, a);
}

function check_step2(a) {
    if(loggedIn) {
        $('#pre_uid').html('' + firebase.auth().currentUser.email + '');
    } else {
        $('#pre_uid').html('"COMPLETE STEP ONE FIRST"');
    }
    BloxAdmin.Data.get("/roblox", {}, data => {
        if(data.isLinked) {
            $('#card_step2>.card-header').attr('data-background-color', 'green');
            $('#card_step2>.card-header>.material-icons').html('check_box');
            $('#card_step2>.card-content>button').addClass('hide');
            s2Complete = true;
        }
        
        gratz(data.isLinked, a);
    });
}

function check_step3(a) {
    BloxAdmin.Data.get("/places", {}, data => {
        if(data.totalPlaces > 0) {
            $('#card_step3>.card-header').attr('data-background-color', 'green');
            $('#card_step3>.card-header>.material-icons').html('check_box');
            $('#card_step3>.card-content>button').addClass('hide');
            s3Complete = true;
        }
        gratz(data.totalPlaces > 0, a);
    });
}

check_step1(true);
check_step2(true);
check_step3(true);
</script>