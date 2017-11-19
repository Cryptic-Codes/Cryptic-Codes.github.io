<?php
global $wikiRow;
?>
<form id="editWikiPage">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group label-floating">
                <label class="control-label">Title</label>
                <input type="text" id="wiki_title" class="form-control" value="<?php echo $wikiRow['title']; ?>" required>
            <span class="material-input"></span></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group label-floating">
                <label class="control-label">Subtitle</label>
                <input type="text" id="wiki_subtitle" class="form-control" value="<?php echo $wikiRow['subtitle']; ?>">
            <span class="material-input"></span></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group label-floating is-empty">
                <label class="control-label">Content</label><br/>
                <div contenteditable="true" class="card" id="wiki_content"><?php echo $wikiRow['content']; ?></div>
            <span class="material-input"></span></div>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-success btn-round">Update</button><!--<a onclick="BloxAdmin.Page.reload()" class="btn btn-danger btn-round">Refresh</a>-->
    </div>
</form>
<script>
// editor = CKEDITOR.replace("wiki_content");
CKEDITOR.disableAutoInline = true;
editor = CKEDITOR.inline( 'wiki_content' );

$('#editWikiPage').on('submit', function(event) {
   event.preventDefault();
   var d = {
       content: editor.getData(),
       title: $('#wiki_title').val(),
       subtitle: $('#wiki_subtitle').val(),
       "short": "<?php echo $wikiRow['short']; ?>"
   }
   BloxAdmin.Data.get("/updatewiki", d, t => {
       if(t.code == 200) {
            $.notify({
                icon: "update",
                message: 'Updated Wiki Page'
            },{
                type: 'success'
            });
            console.log(t);
            BloxAdmin.Page.go("/wiki/<?php echo $wikiRow['short']; ?>")
       } else {
            $.notify({
                icon: "error",
                message: 'Failed to update page! ' + t.message            
            },{
                type: 'danger'
            });
       }
   }, e => {
        $.notify({
            icon: "error",
            message: 'Failed to update page!'          
        },{
            type: 'danger'
        });
   });
});
</script>