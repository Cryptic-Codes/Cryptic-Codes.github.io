<?php
global $out;
global $card2;
?>
<table class="table table-hover">
    <thead class="text-danger">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th class="text-center">Online Users</th>
            <th class="text-center"></th>
        </tr>
    </thead>
    <tbody>
<?php
if($out['places']['totalPlaces'] === 0) {
echo '<tr>
	<td>-</td>
	<td>You have no places</td>
	<td class="text-center">-</td>
</tr>';
} else {
foreach($out['places']['places'] as $placeId) {
    $asset = json_decode(file_get_contents("http://api.roblox.com/marketplace/productinfo?assetId=" . $placeId), true);
    $place = json_decode(file_get_contents("https://blox.al1l.com/__/data/place?place=" . $placeId), true);
    echo '<tr>
	<td><a href="/place/'.$placeId.'">'.$placeId.'</a></td>
	<td><a href="/place/'.$placeId.'">'.$asset['Name'].'</a></td>
	<td class="text-center"><a href="/place/'.$placeId.'">'.$place['onlinePlayers'].'</a></td>
	<td class="text-center"><a href="/place/'.$placeId.'"><i class="material-icons">open_in_new</i></a></td>
</tr>';
}
}
?>
    </tbody>
</table>