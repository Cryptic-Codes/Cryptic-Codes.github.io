<?

function get_script_output($file) {
    $old = ob_get_contents();
    ob_end_clean();
    if(!is_file($file)) {
        return '<pre>File not found.</pre>';
    }
    
    global $out;
    global $args;
    global $db;
    global $user;
    
    ob_start();
    include($file);
    $rtn = ob_get_contents();
    ob_end_clean();
    ob_start();
	echo $old;
    return $rtn;
}

function build_fill_title_card() {
    
}

function build_icon_title_card($size, $color, $icon, $category, $title, $footer=false, $plain=false) {
    
}

class Card {
    public $size = 'col-12';
    public $color = 'red';
    public $is_full_title = false;
    public $title;
    public $category;
    public $is_plain = false;
    public $has_footer = false;
    public $has_title = true;
    public $footer_icon = array('', '');
    public $footer_html;
    public $content_class = "card-content";
    public $header_class = "card-header";
    public $content;
    public $icon_class;
    public $icon_html;
    
    function size($size) { $this->size = $size; return $this; }
    function color($color) { $this->color = $color; return $this; }
    function full($is_full_title) { $this->is_full_title = $is_full_title; return $this; }
    function title($title) { $this->title = $title; return $this; }
    function category($category) { $this->category = $category; return $this; }
    function plain($is_plain) { $this->is_plain = $is_plain; return $this; }
    function footer_icon($footer_icon) { $this->footer_icon = $footer_icon; return $this; }
    function footer($footer_html) { $this->footer_html = $footer_html; return $this; }
    function has_footer($has_footer) { $this->has_footer = $has_footer; return $this; }
    function has_title($has_title) { $this->has_title = $has_title; return $this; }
    function content_class($content_class) { $this->content_class = $content_class; return $this; }
    function header_class($header_class) { $this->header_class = $header_class; return $this; }
    function content($content) { $this->content = $content; return $this; }
    function icon_class($icon_class) { $this->icon_class = $icon_class; return $this; }
    function icon_html($icon_html) { $this->icon_html = $icon_html; return $this; }
    
    function build($raw = false) {
        $sb = '<div class="' . $this->size . '"><div class="card';
        if($this->is_plain) {
            $sb .= ' card-plain';
        }
        if(!$this->is_full_title) {
            $sb .= ' card-stats';
        }
        $sb .= '">';
        
        if($this->has_title) {
            $sb .= '<div class="' . $this->header_class . '" data-background-color="' . $this->color . '">';
            
            if($this->is_full_title) {
                $sb .= '<h4 class="title">' . $this->title . '</h4>';
                $sb .= '<p class="category">' . $this->category . '</p>';
            } else {
                $sb .= '<i class="' . $this->icon_class . '">' . $this->icon_html . '</i>';
            }
            $sb .= '</div>';
        }
        
        $sb .= '<div class="' . $this->content_class . '">';
        
        if($this->is_full_title) {
            $sb .= $this->content;
        } else {
            $sb .= '<p class="category">' . $this->category . '</p>';
            $sb .= '<h3 class="title">' . $this->title . '</h3>';
            $sb .= $this->content;
        }
        $sb .= '</div>';
        
        if($this->has_footer) {
            $sb .= '<div class="card-footer"><div class="stats">';
            $sb .= '<i class="' . $this->footer_icon[0] . '">' . $this->footer_icon[1] . '</i> ';
            $sb .= '' . $this->footer_html . '</div></div>';
        }
        
        $sb .= '</div></div>';
        if(!$raw)
            $sb = base64_encode($sb);
        return $sb;
    }

}

function start_div($cls, $raw = false) {
    $sb = '<div class="'.$cls.'">';
    if(!$raw)
        $sb = base64_encode($sb);
    return $sb;
}

function end_div($raw = false) {
    $sb = '</div>';
    if(!$raw)
        $sb = base64_encode($sb);
    return $sb;
}

function httpPost($url, $params) {
    $ch = curl_init();  
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $output = curl_exec($ch);
	if(curl_error($ch)) {
		die('An error occurred while contacting the application API:' . curl_error($ch));
	}
    curl_close($ch);
	return $output;
}

class Modal {
    public $id = 'modal';
    public $title = 'red';
    public $content = '';
    public $buttons = '';
    
    function rand_id() { $this->id = 'modal_' . strval(rand(1000, 9999)); return $this;}
    function id($id) { $this->id = $id; return $this; }
    function buttons($buttons) { $this->buttons = $buttons; return $this; }
    function content($content) { $this->content = $content; return $this; }
    function title($title) { $this->title = $title; return $this; }
    
    function build($raw = false) {
        $sb = '<div id="'.$this->id.'" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">'.$this->title.'</h4>
      </div>
      <div class="modal-body">
        '.$this->content.'
      </div>
      <div class="modal-footer">
        '.$this->buttons.'
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>';
        
        if(!$raw)
            $sb = base64_encode($sb);
        return $sb;
    }

}

class UUID {
  public static function v3($namespace, $name) {
    if(!self::is_valid($namespace)) return false;

    // Get hexadecimal components of namespace
    $nhex = str_replace(array('-','{','}'), '', $namespace);

    // Binary Value
    $nstr = '';

    // Convert Namespace UUID to bits
    for($i = 0; $i < strlen($nhex); $i+=2) {
      $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
    }

    // Calculate hash value
    $hash = md5($nstr . $name);

    return sprintf('%08s-%04s-%04x-%04x-%12s',

      // 32 bits for "time_low"
      substr($hash, 0, 8),

      // 16 bits for "time_mid"
      substr($hash, 8, 4),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 3
      (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

      // 48 bits for "node"
      substr($hash, 20, 12)
    );
  }

  public static function v4() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

      // 32 bits for "time_low"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff),

      // 16 bits for "time_mid"
      mt_rand(0, 0xffff),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand(0, 0x0fff) | 0x4000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand(0, 0x3fff) | 0x8000,

      // 48 bits for "node"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
  }

  public static function v5($namespace, $name) {
    if(!self::is_valid($namespace)) return false;

    // Get hexadecimal components of namespace
    $nhex = str_replace(array('-','{','}'), '', $namespace);

    // Binary Value
    $nstr = '';

    // Convert Namespace UUID to bits
    for($i = 0; $i < strlen($nhex); $i+=2) {
      $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
    }

    // Calculate hash value
    $hash = sha1($nstr . $name);

    return sprintf('%08s-%04s-%04x-%04x-%12s',

      // 32 bits for "time_low"
      substr($hash, 0, 8),

      // 16 bits for "time_mid"
      substr($hash, 8, 4),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 5
      (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

      // 48 bits for "node"
      substr($hash, 20, 12)
    );
  }

  public static function is_valid($uuid) {
    return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
                      '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
  }
}

function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}

function get_404_Page() {
    global $args;
    $a = $args;
    array_shift($a);
    $url = '';
    foreach($a as $arg) {
        $url .= '/' . $arg;
    }
    $out = array();
    $out['nav_title'] = 'Blox Admin';
    $out['window_title'] = 'BloxAdmin | 404';
    $out['cards_title'] = 'Not Found';
    $out['active_nav'] = 'none';
    $out['url'] = $url;
    $out["cards"] = array(base64_encode('<div class="col-md-6 col-md-offset-3"><div class="card"><div class="card-content"><div class="text-center"><h3><i class="fa fa-question"></i>&nbsp; Error 404</h3><p>The requested page was not found.</p></div></div></div></div>'));
    return $out;
}

function get_error($code = 400) {
    global $args;
    switch($code) {
        case 500:
            $message = "Server Error";
            break;
        case 401:
            $message = "Forbidden";
            break;
        case 404:
            $message = "Not Found";
            break;
        default:
            $message = "Invalid Input";
            break;
    }
    return array(
        'code' => $code,
        'message' => $message
    );
}