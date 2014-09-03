$(function(){

    $('.toggle-uml-settings').on('click', function(e){
        e.preventDefault();
        var $form = $('.uml-settings');
    //    $('.uml-settings').slideToggle('fast');
        if($form.hasClass('hidden')){
            $form.hide();
            $form.removeClass('hidden');
        }
        $form.toggle('fast');    
    });

    var $textarea = $('.preview-block textarea');
    
    $('.should-confirm').on('click', function(e){
       return confirm('Are you sure to continue?'); 
    });
    
//    $textarea.css('height', 'auto');
//    $textarea.elastic();

});