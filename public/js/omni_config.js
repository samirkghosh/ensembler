/**
 * Auth: Vastvikta Nishad 
 * Date: 05/03/2024
 * this js file is for handling  editing and deleting for Omnichannel configuration 
 */
jQuery(function ($){
    var Admin = {
        init: function (){
            jQuery("body").on('click','.delete_channel',this.HandleConfigDelete);
    }, 
    HandleConfigDelete: function () {
        console.log("Config Delete Function Triggered");
        if (confirm("Are you sure to delete this ?")) {
            let id = $(this).data('id');
            let channel = $(this).data('channel'); 
            console.log(id);
            console.log(channel);
            $.ajax({
                method: 'POST',
                url: 'omnichannel_config/config_function.php',
                data: { 'id': id, 'channel': channel, 'action': 'ChannelDelete' },
                success: function (response) {
                    $('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Deletion Successful</div>');
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                }
            });
        }
    }
}
    Admin.init();
});