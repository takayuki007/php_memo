$(function(){
  $('textarea').bind('keyup',function(){
    var count = $(this).val().length;
    $('.js-count').text(count);
  })

  $('.js-delete').click(function(){
    if(!confirm('本当に削除しますか？')){
      return false;
    }else {
      location.href = index.php;
    }
  })

  var $dropArea = $('.area-drop');
  var $fileInput = $('.input-file');

  $$dropArea.on('click',function(e){
    e.stopPropagetion();
    e.preventDefault();
    $(this).css('border','3px #ccc dashed');
  });

  $dropArea.on('dragleave',function(e){
    e.stopPropagetion();
    e.preventDefault();
    $(this).css('border','none');
  });

  $fileInput.on('change',function(e){
    $$dropArea.css('border','none');
    var file = this.files[0];
    $img = $(this).siblings('.prev')
    fileReader = new FileReader();

    fileReader.onload = function(event){
      $img.attr('src',event.target.result).show();
    };

    fileReader.readAsDataURL(file);
  });
  
})
