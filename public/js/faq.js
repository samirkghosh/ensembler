// JavaScript code for highlighting search results (Knowledge Base)
var oldVal;
var new_submit_form = $('#seabutton').val();
if(new_submit_form){
    var currentVal = $('#seabutton').val();
    oldVal = currentVal;
    var data = $('.new_table').html();
    console.log('i am here');
    console.log(data);
    console.log(oldVal);
    console.log(currentVal);
    var text = $(".new_table").html(data).find(`p:contains(${currentVal})`);
    console.log(text);
    if(text.length>0){
       $(".new_table")
      .html(data)
      .find(`p:contains(${currentVal})`)
      .html((idx,old) => old
        .split(currentVal)
        .join(`<span class="highlighted">${currentVal}</span>`)
      );
    }else{
      var currentVal = (currentVal.toUpperCase());
        $(".new_table")
        .html(data)
        .find(`p:contains(${currentVal})`)
        .html((idx,old) => old
          .split(currentVal)
          .join(`<span class="highlighted">${currentVal}</span>`)
        );
        console.log('not here');
    }
}