<?php
global $out;
global $card7;
// <div class="row">
    // <ul class="nav navbar-nav">
        // <!-- <li class="dropdown">
            // <a href="#" class="dropdown-toggle ignore-link text-warning" data-toggle="dropdown" aria-expanded="true">
                // <i class="material-icons">filter_list</i>
                // Filters
            // <div class="ripple-container"></div></a>
            // <ul class="dropdown-menu" id="filters">
                // <li><a href="#" class="ignore-link" id="filter_1"><i class="material-icons">radio_button_checked</i>&nbsp; Only open</a></li>
                // <li><a href="#" class="ignore-link" id="filter_2"><i class="material-icons">radio_button_unchecked</i>&nbsp; Only Closed</a></li>
                // <li><a href="#" class="ignore-link" id="filter_3"><i class="material-icons">radio_button_unchecked</i>&nbsp; All servers</a></li>
            // </ul>
        // </li> -->
        // <li>
			// <a href="#" class="dropdown-toggle ignore-link" data-toggle="dropdown" id="filter_refresh">
				// <i class="material-icons">refresh</i>
				// Refresh
                // <div class="ripple-container"></div>
            // </a>
		// </li>
	// </ul>
// </div>
?>
<script>
$('ul#filters>li>a').on('click', event => {
    filterId = parseInt(event.target.id.split('_')[1]);
    refreshTable(filterId);
});

$('a#filter_refresh').on('click', event => {
    refreshTable(filterId);
});

function refreshTable(filterId) {
    var filterNames = ['&nbsp; Only open', '&nbsp; Only Closed', '&nbsp; All servers']
    var checked = '<i class="material-icons">radio_button_checked</i>';
    var unchecked = '<i class="material-icons">radio_button_unchecked</i>';
    var defaultRow = '<tr><td>You have no servers to show.</td><td class="text-center">-</td><td class="text-center">-</td><td class="text-center">-</td></tr>';
    var placeLink = current_page.url;
    
    $('ul#filters>li>a>i.material-icons').html('radio_button_unchecked');
    
    $('ul#filters>li>a#filter_' + filterId).html(checked + filterNames[filterId-1]);
    
    var showOpen = (filterId == 1); // False
    var showClosed = (filterId == 2); // True
    var showBoth = (filterId == 3);
    
     BloxAdmin.Data.get("/place?place=<?php echo $out['place']['placeId']; ?>", {}, placeData => {
        current_page.place = placeData;
        tableBody = "";
        if(placeData.totalServers == 0) {
            tableBody = defaultRow;
            $('#table_servers').html(tableBody)
        } else if(showOpen && placeData.openServers == 0) {
            tableBody = defaultRow;
            $('#table_servers').html(tableBody)
        } else if(showClosed && (placeData.totalServers - placeData.openServers) == 0) {
            tableBody = defaultRow;
            $('#table_servers').html(tableBody)
        }
        $.each(placeData.servers, function(serverId, isOpen) { // False
        
            if (isOpen == (showOpen && !showClosed) || showBoth) {
                 BloxAdmin.Data.get("/server?server=" + serverId, {}, serverData => {
                    current_page.place.servers[serverId] = serverData;
                    var serverLink = "#\" onclick=\"BloxAdmin.Page.go('" + BloxAdmin.Page.current.url + '/server/' + serverId + "')\"";
                    tableRow = ""
                    tableRow = tableRow + '<tr>';
                    if(isOpen) {
                        tableRow = tableRow + '<td><a href="'+serverLink+'">'+serverId+'</a>&nbsp;<a href="#" class="place-join hide text-success" onclick="joinGameInstance(current_page.place.placeId, current_page.place.servers[\''+serverId+'\'].server.serverInstance)"><i class="material-icons">play_arrow</i> Join</a></td>';
                    } else {
                        tableRow = tableRow + '<td><a href="'+serverLink+'">'+serverId+'</a></td>';
                    }
                    tableRow = tableRow + '<td class="text-center text-'+(isOpen ? 'success' : 'danger')+'"><i class="material-icons">'+(isOpen ? 'check' : 'close')+'</i></td>';
                    tableRow = tableRow + '<td class="text-center"><a href="'+serverLink+'">'+current_page.place.servers[serverId].server.onlinePlayers+'</a></td>';
                    tableRow = tableRow + '<td class="text-center"><a href="'+serverLink+'">'+current_page.place.servers[serverId].server.totalMessages+'</a></td>';
                    tableRow = tableRow + '<td class="text-center"><a href="'+serverLink+'"><i class="material-icons">open_in_new</i></a></td>';
                    tableRow = tableRow + '</tr>';
                    tableBody = tableBody + tableRow;
                    $('#table_servers').html(tableBody)
                    if(showJoinButtons) {
                        $('.place-join').removeClass('hide');
                    }
                });
                //add server
            }
        });
    });
}

filterId = 1;
refreshTable(filterId);
</script>
<div class="row">
    <table class="table table-hover0" style="overflow-x: scroll;">
        <thead class="text-danger">
            <tr>
                <th>ID</th>
                <th class="text-center">Is Open</th>
                <th class="text-center">Online Users</th>
                <th class="text-center">Messages</th>
                <th class="text-center"></th>
            </tr>
        </thead>
        <tbody id="table_servers">
    <?php
    // if($out['place']['openServers'] === 0 && false) {
    // echo '<tr>
        // <td>You have no open servers. Try changing the filters</td>
        // <td class="text-center">-</td>
        // <td class="text-center">-</td>
        // <td class="text-center">-</td>
    // </tr>';
    // } else {
    // foreach($out['place']['servers'] as $serverId => $isOpen) {
        // if(!$out['place']['servers'][$serverId]) {
            // continue;
        // }
        // $out['place']['servers'][$serverId] = json_decode(httpPost("https://blox.al1l.com/__/data/server?server=" . $serverId, array(
            // 'uid' => $user['uid']
        // )), true);
        // $svr = $out['place']['servers'][$serverId]['server'];
        // $placeId = $out['place']['placeId'];
        // echo '<tr>
        // <td><a href="/place/'.$placeId.'/'.$serverId.'">'.$serverId.'</a></td>';
        
        // if($isOpen){
            // echo '<td class="text-center text-success"><i class="material-icons">check</i></td>';
        // } else {
            // echo '<td class="text-center text-danger"><i class="material-icons">close</i></td>';
        // }
        
        // echo '
        // <td class="text-center"><a href="/place/'.$placeId.'/'.$serverId.'">'.$svr['onlinePlayers'].'</a></td>
        // <td class="text-center"><a href="/place/'.$placeId.'/'.$serverId.'">'.$svr['totalMessages'].'</a></td>
        // <td class="text-center"><a href="/place/'.$placeId.'/'.$serverId.'"><i class="material-icons">open_in_new</i></a></td>
    // </tr>';
    // }
    // }
    ?>
        </tbody>
    </table>
</div>