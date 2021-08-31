<link href="<?php echo base_url();?>assets/rating.css" rel="stylesheet" type="text/css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/rating.js"></script>


<script>
$( document ).ready(function() {
    $(function() {
        $("#rating_star").codexworld_rating_widget({
            starLength: '5',
            initialValue: '',
            callbackFunctionName: 'processRating',
            imageDirectory: 'images/',
            inputAttr: 'postID'
        });
    });
    
    function processRating(val, attrVal){
        $.ajax({
            type: 'POST',
            url: 'rating.php',
            data: 'postID='+attrVal+'&ratingPoints='+val,
            dataType: 'json',
            success : function(data) {
                if (data.status == 'ok') {
                    alert('You have rated '+val+' to CodexWorld');
                    $('#avgrat').text(data.average_rating);
                    $('#totalrat').text(data.rating_number);
                }else{
                    alert('Some problem occured, please try again.');
                }
            }
        });
    }
});
</script>