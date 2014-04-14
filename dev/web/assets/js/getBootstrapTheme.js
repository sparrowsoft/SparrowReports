//Get custom bootstrap theme from bootswatch.com api and set is as active.

function setBootstrapTheme(themeId){
    console.groupCollapsed("Bootstrap theme");
    console.time("Loading time");
    
    themeId = typeof themeId !== 'undefined' ? themeId : 7; //set 7 as default value
    console.log("%cLoading custom bootstrap theme...", "color: blue;"); 

    jQuery.get("http://api.bootswatch.com/3/", function (data) {
      var themes = data.themes;
      jQuery("link").attr("href", themes[themeId].css).addClass('test');
      jQuery('#preloader').fadeOut(1000);
      
    }, "json").fail(function(){
        console.log("%cSomething went wrong with the bootstrap custom theme JSON!", "color: red;"); 
    });
    
    console.log("%cCustom theme applied.", "color: green;"); 
    console.timeEnd("Loading time");
    
    console.groupEnd();
}