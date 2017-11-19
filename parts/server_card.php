<?php
global $out;
global $user;
?>
<div class="row">
<div class="col-sm-4 text-center">
<h3><?php echo $out['server']['onlinePlayers']; ?><h3>
<h6>Playing<h5>
</div>
<div class="col-sm-4 text-center">
<h3><?php echo $out['server']['totalMessages']; ?><h3>
<h6>Messages<h5>
</div>
<div class="col-sm-4 hidden-xs text-center">
<h3 class="<?php echo ($out['place']['code'] === 200 ? 'text-success' : 'text-error'); ?>"><?php echo ($out['place']['code'] === 200 ? 'Good' : 'Error'); ?><h3>
<h6>API Status<h5>
</div>
</div>