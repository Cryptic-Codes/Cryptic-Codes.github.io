<?php
?>
<div class="row">                   
    <div class="col-md-12">
        <div class="card card-profile">
            <div class="card-avatar">
                <a href="#pablo">
                    <img class="img" style="background-color: white;" src="<?php echo $out['blox']['thumb']; ?>" id="user_thumb">
                </a>
            </div>
            <div class="content">
                <h6 class="category text-gray" id="user_role"></h6>
                <h4 class="card-title" id="user_name"><i class="fa fa-refresh fa-spin"></i> Loading</h4>
                <p class="card-content" style="white-space: pre-wrap;" id="user_desc"></p>
            </div>
        </div>
    </div>
</div>
<script>
BloxAdmin.Data.get('/roblox-user/<?php echo $args[2]; ?>', {}, data => {
	$('#user_desc').html(data.description);
	$('#user_role').html(data.role);
	$('#user_name').html(data.Username);
});
</script>