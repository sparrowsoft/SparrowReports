loadFiles([ //JS and CSS to load when preloading
    "/web/assets/js/lessInit.js",
    "//cdnjs.cloudflare.com/ajax/libs/less.js/1.7.0/less.js",
    "//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.0/jquery.min.js",
    "//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/js/bootstrap.min.js",
    "//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css",
    "/web/assets/css/fullcalendar.css",
    "/web/assets/js/bootstrap-datepicker.js",
    "/web/assets/js/getBootstrapTheme.js",
    "//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,900&subset=latin,latin-ext",
    "//brianreavis.github.io/selectize.js/js/selectize.js",
    "/web/assets/js/globalScripts.js",
    "/web/assets/js/jquery.slimscroll.min.js",
    "/web/assets/js/reports.js",
    "/web/assets/js/fullcalendar.js"
]);

//init functions
head.ready(function(){
    setBootstrapTheme();
});

function loadFiles(filesToLoad){
    console.groupCollapsed("Head loading");
    console.time("Files loading time");
    for (var i=0,  tot=filesToLoad.length; i < tot; i++) {
        var currentFile = filesToLoad[i];
        head.load(currentFile, (function(filePath){     
                console.log("Done loading "+currentFile.replace(/^.*[\\\/]/, ''));
            return function(){filePath;};
        })(currentFile));
      }
      head.ready(function(){
          console.log("Head files loaded");
          console.timeEnd("Files loading time");
          console.groupEnd();
      });
}