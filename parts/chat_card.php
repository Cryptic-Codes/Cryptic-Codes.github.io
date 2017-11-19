<hr/>
<div id="chatBox"></div>
<hr/>
<form id="chatBox_form">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group label-floating is-empty">
                <label class="control-label">Send Message</label>
                <input type="text" class="form-control" id="chatBox_message" required="">
            <span class="material-input"><small>Press enter to send</small></span></div>
        </div>
    </div>
</form>
<script>
color = {
    r: 244,
    b: 67,
    g: 54
}
chat_url = BloxAdmin.Page.current.url;
chat = BloxAdmin.Chat.init("#chatBox", {});
chat.from(BloxAdmin.Page.current.server.uuid)
chat.afterCall = function(data) {
    if(!data.serverOpen || BloxAdmin.Page.current.url != chat_url) {
        chat.stopSync();
        if(!data.serverOpen) {
            $.notify({
                icon: "error",
                message: 'That server has shutdown.'
            
            },{
                type: 'warning'
            });
            if(BloxAdmin.Page.current.url == chat_url) {
                 BloxAdmin.Page.go("/place/" + BloxAdmin.Page.current.place.placeId);
            }
        }
    }
};
chat.startSync();

$('#chatBox_form').on('submit', function(event){
    console.log('submit');
    event.preventDefault();
    message = $('#chatBox_message').val();
    $('#chatBox_message').val('');
    chat.sendMessageToServer("[<?php echo $out['place']['asset']['Creator']['Name']; ?>]: " + message, color, data => {
        
    });
});
</script>