<?php
global $out;
global $user;
?>
<div class="row">
    <div class="col-lg-6">
        <h3 class="title text-left"><? echo $out['place']['asset']['Name']; ?></h3>
        <h4 class="title text-left">By <a href="/user/<? echo $out['place']['asset']['Creator']['Id']; ?>"><? echo $out['place']['asset']['Creator']['Name']; ?></a></h4>
    </div>
    <div class="col-lg-6">
        <br/>
        <button href="#" class="ignore-link btn btn-info btn-block" id="joinPlace" onclick="BloxAdmin.Roblox.joinPlace(current_page.place.placeId)">Play as Guest</button>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-12">
    <p style="white-space: pre-wrap;" class="text-left"><? echo $out['place']['asset']['Description']; ?></p>
    </div>
</div>
<hr/>
<div class="row">
<div class="col-sm-3 text-center">
<h3><a href="/place/<? echo $out['place']['placeId']; ?>/players"><?php echo $out['place']['onlinePlayers']; ?></a><h3>
<h6>Playing<h5>
</div>
<div class="col-sm-3 text-center">
<h3><?php echo $out['place']['openServers']; ?><h3>
<h6>Servers<h5>
</div>
<div class="col-sm-3 text-center">
<h3><?php echo $out['place']['totalMessages']; ?><h3>
<h6>Messages<h5>
</div>
<div class="col-sm-3 hidden-xs text-center">
<h3 class="<?php echo ($out['place']['code'] === 200 ? 'text-success' : 'text-error'); ?>"><?php echo ($out['place']['code'] === 200 ? 'Good' : 'Error'); ?><h3>
<h6>API Status<h5>
</div>
</div>