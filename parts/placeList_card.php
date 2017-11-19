<?php
global $out;
global $card2;


$userStats = json_decode(httpPost("https://blox.al1l.com/__/data/user-bundle?min=true", array(
    'uid' => $user['uid'],
    'select' => 'totalPlaces, places'
)), true)['bundle'];

// echo "<script>console.log(".json_encode($userStats).")</script>";

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
if($userStats['totalPlaces'] == 0) {
echo '<tr>
	<td>-</td>
	<td>You have no places</td>
	<td class="text-center">-</td>
</tr>';
} else {
if($out['places']['code'] === 200) {
    foreach($userStats['places'] as $placeId) {
		$place = json_decode(httpPost("https://blox.al1l.com/__/data/place-bundle?min=true", array(
		    'uid' => $user['uid'],
		    'subject' => $placeId,
		    'select' => 'onlineUsers'
		)), true)['bundle'];
		
        $asset = json_decode(file_get_contents("http://api.roblox.com/marketplace/productinfo?assetId=" . $placeId), true);
        echo '<div class="hide"><pre class="note">' . print_r($place, true) . '</pre></div>';
        echo '<tr>
        <td><a href="/place/'.$placeId.'">'.$placeId.'</a></td>
        <td><a href="/place/'.$placeId.'">'.$asset['Name'].'</a></td>
        <td class="text-center"><a href="/place/'.$placeId.'">'.$place['onlineUsers'].'</a></td>
        <td class="text-center"><a href="/place/'.$placeId.'"><i class="material-icons">open_in_new</i></a></td>
    </tr>';
    }
} else {
        echo '<tr>
        <td>Error, try signing out and back in.</td>
        <td>-</td>
        <td class="text-center">-</td>
    </tr>';
}
}
?>
    </tbody>
</table>