<?php

switch(strtolower($args[1])) {
    case 'wiki':
        $out['nav_title'] = 'Blox Admin';
        $out['window_title'] = 'BloxAdmin | Wiki';
        $out['cards_title'] = 'Wiki';
        $out['search_enabled'] = true;
        $out['active_nav'] = '#nav_wiki';
        $edit = false;
        if(end($args) == "edit") {
            array_pop($args);
            $edit = true;
        }
        array_shift($args);
        array_shift($args);
        $short = '';
        foreach($args as $arg) {
            if($short === '') {
                $short .= $arg;
            } else {
                $short .= '/' . $arg;
            }
        }
        if($short == "")
            $short = "home";
        $out['short'] = $short;
        $out['args'] = $args;
        $q = "SELECT * FROM b_wiki WHERE `short` ='".strtolower($short)."';";
        $r = $db->query($q);
            $card1 = new Card();
        if($r->rowCount() != 0) {
            $wikiRow = $r->fetch(PDO::FETCH_ASSOC);
            $out['wiki'] = $wikiRow;
            
            if($edit) {
                $out['cards_title'] = 'Wiki Edit';
                $out["cards"] = array(
                    $card1->full(true)
                          ->title('Edit page')
                          ->category($wikiRow['title'])
                          ->size("col-lg-12")
                          ->content(get_script_output('parts/wikiedit_card.php'))
                          ->build()
                );
            } else {
                $title = $wikiRow['title'];
                if(isset($user)) {
                    // if($user['role'] == "administrator") {
                    if(has_permission('edit_wiki')) {
                        $title .= '<a rel="tooltip" title="" href="'.$short.'/edit" class="btn btn-success btn-xs btn-round pull-right" data-original-title="Edit"><i class="material-icons">edit</i></a>';
                    }   
                }
                $out['cards_title'] = 'Wiki';
                $out["cards"] = array(
                    $card1->full(true)
                          ->title($title)
                          ->category($wikiRow['subtitle'])
                          ->size("col-lg-12")
                          ->content($wikiRow['content'])
                          ->build()
                );
            }
        } else {
            $out['cards_title'] = 'Wiki Not Found';
            $q = "SELECT * FROM b_wiki WHERE `short` ='404';";
            $r = $db->query($q);
            if($r->rowCount() != 0) {
                $wikiRow = $r->fetch(PDO::FETCH_ASSOC);
                $out['wiki'] = $wikiRow;
                $out["cards"] = array(
                    $card1->full(true)
                          ->title($wikiRow['title'])
                          ->category($wikiRow['subtitle'])
                          ->size("col-lg-12")
                          ->content($wikiRow['content'])
                          ->build()
                );
            } else {
                $out = get_404_Page();
                $out['active_nav'] = '#nav_wiki';
            }
        }
        $out['url'] = '/wiki/' . $short;
        if($edit)
            $out['url'] .= '/edit';
        break;
    case 'account':
        switch(strtolower($args[2])) {
            case 'signin':
                $out['nav_title'] = 'Blox Admin';
                $out['window_title'] = 'BloxAdmin | Signin';
                $out['cards_title'] = 'Signin';
                $out['search_enabled'] = false;
                $out['active_nav'] = '#nav_account';
                $out['url'] = '/account/signin';
                $card1 = new Card();
                $card2 = new Card();
                $card3 = new Card();
                $modal1 = new Modal();
                $out["modals"] = array(
                    $modal1->rand_id()
                           ->title('Forgot Password')
                           ->content(get_script_output('parts/forgotpassword_modal.php'))
                           ->build()
                );
                $modal1Id = $modal1->id;
                $out["cards"] = array(
                    $card3->full(true)
                          ->title("Signin")
                          ->size("col-lg-4 col-md-12 col-sm-12 col-xs-12 pull-right")
                          ->content(get_script_output('parts/signin_card.php'))
                          ->build(),
                    $card2->full(true)
                          ->title("BloxAdmin")
                          ->category("Helping ROBLOX get even safer.")
                          ->size("col-lg-8 col-md-12 col-sm-12 col-xs-12 hidden-md hidden-sm hidden-xs")
                          ->content(get_script_output('parts/about_card.php'))
                          ->build(),
                    $card1->full(true)
                          ->title("Signup")
                          ->category("Use email or any other signin provider")
                          ->size("col-lg-4 col-md-12 col-sm-12 col-xs-12 pull-right")
                          ->content(get_script_output('parts/signup_card.php'))
                          ->build(),
                    $card2->full(true)
                          ->size("col-lg-8 col-md-12 col-sm-12 col-xs-12 hidden-lg")
                          ->build());
                break;
            default:
                $out = get_404_Page();
                break;
        }
        break;
    case 'getting-started':
        $out['places'] = json_decode(httpPost("https://blox.al1l.com/__/data/places", array(
            'uid' => $user['uid']
        )), true);
        if(get_user_places()['totalPlaces'] != 0) {
            $out['redirect'] = '/dashboard';
            break;
        }
        $out['nav_title'] = 'Blox Admin';
        $out['window_title'] = 'BloxAdmin | Getting Started';
        $out['cards_title'] = 'Getting Started';
        $out['search_enabled'] = true;
        $out['active_nav'] = '#nav_dashboard';
        $out['url'] = '/getting-started';
        $card1 = new Card();
        $out["cards"] = array(
            $card1->full(true)
                ->title("BloxAdmin")
                ->category("Helping ROBLOX get even safer.")
                ->size("col-xs-12")
                ->content(get_script_output('parts/accountSetup_card.php'))
                ->build());
        break;
    case 'dashboard':
        $out['places'] = json_decode(httpPost("https://blox.al1l.com/__/data/places", array(
            'uid' => $user['uid']
        )), true);
        if($out['places']['totalPlaces'] === 0) {
            $out['redirect'] = '/getting-started';
            break;
        }
        $out['nav_title'] = 'Blox Admin';
        $out['window_title'] = 'BloxAdmin | Dashboard';
        $out['cards_title'] = 'Dashboard';
        $out['search_enabled'] = true;
        $out['active_nav'] = '#nav_dashboard';
        $out['url'] = '/dashboard';
        $card1 = new Card();
        $card2 = new Card();
        $out["cards"] = array(
            start_div("row"),
            $card1->title($out['places']['totalPlaces'])
                  ->category("Total Places")
                  ->icon_class("material-icons")
                  ->icon_html("place")
                  ->size("col-lg-offset-4 col-lg-4 col-md-6 col-sm-6")
                  ->has_footer(true)
                  ->footer("Just Updated")
                  ->footer_icon(array('material-icons', 'update'))
                  ->build(),
            end_div(),
            start_div("row"),
            $card2->full(true)
                  ->size("col-lg-12")
                  ->title("Places")
                  ->category("A list of all your places")
                  ->content(get_script_output('parts/placeList_card.php'))
                  ->build(),
            end_div()
        );
        break;
    case 'user':
		if(!isset($args[2])) {
			$out['redirect'] = '/dashboard';
			break;
		}
        $out['blox'] = json_decode(file_get_contents("https://api.roblox.com/Users/".$args[2]), true);
        $out['blox']['thumb'] = json_decode(file_get_contents("https://www.roblox.com/headshot-thumbnail/json?userId=".$args[2]."&height=150&width=150"), true)['Url'];
        $out['nav_title'] = 'Blox Admin';
        $out['window_title'] = 'BloxAdmin | User';
        $out['cards_title'] = 'User';
        $out['search_enabled'] = true;
        $out['active_nav'] = '#nav_users';
        $out['url'] = '/user/' . $args[2];
        $out["cards"] = array(base64_encode(get_script_output('parts/user_page.php')));
        break;
    case 'place':
        if(!isset($args[2])) {
            $out['msg'] = "1";
            $out['redirect'] = "/dashboard";
        } else {
            $out['place'] = json_decode(httpPost("https://blox.al1l.com/__/data/place?place=" . $args[2], array(
                'uid' => $user['uid']
            )), true);
            // $userPlaces = json_decode(httpPost("https://blox.al1l.com/__/data/places", array(
                // 'uid' => $user['uid']
            // )), true);
            if($out['place']['code'] !== 200) {
                $out['msg'] = "2";
                $out['redirect'] = '/dashboard';
                break;
            } elseif($out['place']['totalPlaces'] === 0) {
                $out['redirect'] = '/getting-started';
                break;
            }
            $out['place']['asset'] = json_decode(file_get_contents("http://api.roblox.com/marketplace/productinfo?assetId=" . $args[2]), true);
            $out['place']['asset']['thumbnail'] = json_decode(file_get_contents("https://www.roblox.com/asset-thumbnail/json?assetId=" . $args[2] . '&width=768&height=432&format=png'), true)['Url'];
            if(isset($args[3])){
                switch(strtolower($args[3])) {
                    case "server":
                        if(!isset($args[4])) {
                            $out['redirect'] = "/dashboard";
                            break;
                        }
                        
                        $out['server'] = json_decode(httpPost("https://blox.al1l.com/__/data/server?server=" . $args[4], array(
                            'uid' => $user['uid']
                        )), true);
                        if($out['server']['code'] !== 200) {
                            $out['redirect'] = '/place/'. $args[2];
                            break;
                        }
                        $out['server'] = $out['server']['server'];
                        
                        $out['nav_title'] = 'Blox Admin';
                        $out['window_title'] = 'BloxAdmin | ' . $out['place']['asset']['Name'];
                        $out['cards_title'] = $out['place']['asset']['Name'];
                        $out['search_enabled'] = true;
                        $out['active_nav'] = '#nav_dashboard';
                        $out['url'] = '/place/'. $args[2] .'/server/'. $args[4];
                        $card1 = new Card();
                        $card2 = new Card();
                        $out["cards"] = array(
                            $card1->full(true)
                                ->title($out['place']['asset']['Name'] . '<button class="btn btn-warning pull-right" onclick="BloxAdmin.Data.createTask(\'shutdownServer\', \'\', BloxAdmin.Page.current.server.uuid)">Shutdown</button><button class="btn btn-info pull-right" id="joinPlace" onclick="BloxAdmin.Roblox.joinInstance(current_page.place.placeId, current_page.server.serverInstance)">Play as Guest</button>')
                                ->category('Sever: '.$out['server']['uuid'].'</p><p class="category">By <a href="/user/'.$out['place']['asset']['Creator']['Id'].'">'.$out['place']['asset']['Creator']['Name'].'</a>')
                                ->size("col-md-12 col-lg-12")
                                ->content(get_script_output('parts/server_card.php'))
                                ->build(),
                            $card2->full(true)
                                ->title('Chat')
                                ->size("col-md-12 col-lg-6")
                                ->content(get_script_output('parts/chat_card.php'))
                                ->build()
                        );
                        
                    break;
                    default:
                        $out['redirect'] = '/place/'. $args[2];
                        break;
                }
                break;
            }
            $out['nav_title'] = 'Blox Admin';
            $out['window_title'] = 'BloxAdmin | ' . $out['place']['asset']['Name'];
            $out['cards_title'] = $out['place']['asset']['Name'];
            $out['search_enabled'] = true;
            $out['active_nav'] = '#nav_dashboard';
            $out['url'] = '/place/'. $args[2];
            $card1 = new Card();
            $card2 = new Card();
            $out["cards"] = array(
                $card1->full(true)
                    ->content_class("card-content text-left")
                    ->header_class("card-header text-center")
                    ->title('</h3><img class="hidden-xs" style="height: 300px; width: auto;" src="'.$out['place']['asset']['thumbnail'].'"></img><img class="visible-xs hidden-lg" src="'.$out['place']['asset']['thumbnail'].'"></img>')
                    ->size("col-md-12 col-lg-12")
                    ->content(get_script_output('parts/place_card.php'))
                    ->build(),
                $card2->full(true)
                    ->title('Servers')
                    ->category('List of servers for this place')
                    ->size("col-xs-12")
                    ->content(get_script_output('parts/serverList_card.php'))
                    ->build()
            );
        }
        break;
     default:
        $out = get_404_Page();
        break;
}