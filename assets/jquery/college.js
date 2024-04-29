$(document).ready(function(){
    $("#country").on('change',function(){
        var countryId=$(this).val();
        $.ajax({
            method: "POST",
            url: "includes/handlers/ajax_load_states.php",
            data:{id:countryId},
            dataType:"html",
            success:function(data){
                $("#state").html(data);
            }
        });
    });

    $("#state").on('change',function(){
        var stateId=$(this).val();
        $.ajax({
            method: "POST",
            url: "includes/handlers/ajax_load_states.php",
            data:{stateId:stateId},
            dataType:"html",
            success:function(data){
                $("#college").html(data);
            }
        });
    });
});