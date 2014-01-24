// Adobe Illustrator Script to export PNGs at various widths

function saveDocumentAsPng(fileName, width, height, t) {
    d=app.activeDocument;
    // var fileName = d.fullName.toString();
    if(fileName.lastIndexOf(".") >= 0) { fileName = fileName.substr(0, fileName.lastIndexOf("."));}
    fileName += "_wxh.png".replace("w", width).replace("h", height);

    var exportOptions = new ExportOptionsPNG24();
    exportOptions.transparency=t;
    exportOptions.artBoardClipping=true;
    exportOptions.horizontalScale = exportOptions.verticalScale = 100 * Math.max(width/d.width, height/d.height);
    var file = new File(fileName);

    app.activeDocument = d;
    d.exportFile(file, ExportType.PNG24, exportOptions);
}

var index;
// var fn='/Users/darrell/Desktop/FOSS4G_2014_logo_'
var fn='/Users/darrell/Desktop/logo_'
for (index = 100; index <= 600; index+=50) {
  if (app.activeDocument.height > app.activeDocument.width)  {
      height=index;
      width=Math.round(app.activeDocument.width*(height/app.activeDocument.height));
      saveDocumentAsPng(fn+'vert',width,index,false)
      saveDocumentAsPng(fn+'vert_trans',width,index,true)
    } else {
      width=index;
      height=Math.round(app.activeDocument.height*(width/app.activeDocument.width));
      saveDocumentAsPng(fn+'horiz',index,height,false)
      saveDocumentAsPng(fn+'horiz_trans',index,height,true)
    }
}
