//vim :set tabstop=2 shiftwidth=2 expandtab
/*
 *    I am placing this code in the Public Domain. Do with it as you will.
 *    This software comes with no guarantees or warranties but with
 *    plenty of well-wishing instead!
 */

function supa() {
  this.ping = function( supaApplet ) {
    try {
      // IE will throw an exception if you try to access the method in a 
      // scalar context, i.e. if( supaApplet.pasteFromClipboard ) ...
      return supaApplet.ping();
    } catch( e ) {
      return false;
    }
  };

  this.ajax_post = function(  actionUrl, 
                              bytes,
                              fieldname_filename, 
                              filename, 
                              params ) 
  {
    // sanity checks
    if( !fieldname_filename || fieldname_filename == "" ) {
      throw "Developer Error: fieldname_filename not set or empty";
    }

    if( !filename || filename == "" ) {
      throw "Filename required";
    }

    // some constants for the request body
    //FIXME: make sure boundaryString is not part of bytes or the form values
    var boundaryString = 'AaB03x'+ parseInt(Math.random()*9999999,10); 
    var boundary = '--'+boundaryString;
    var cr= '\r\n';

    // build request body
    var body = '';
    body += boundary + cr;

    if( isArray( params ) ) {
      for( var i = 0; i < params.length; ++i ) {
        body += "Content-disposition: form-data; name=\""+escape(params[i].name)+"\";" + cr;
        body += cr;
        body += encodeURI(params[i].value) + cr;
        body += boundary + cr;
      }
    }

    // add the screenshot as a file
    //FIXME: is this the correct encoding?
    body += "Content-Disposition: form-data; name=\""+escape(fieldname_filename)+"\"; filename=\""+encodeURI(filename)+"\"" + cr;
    body += "Content-Type: application/octet-stream" + cr;
    body += "Content-Transfer-Encoding: base64" + cr;
    body += cr;
    body += bytes + cr;
    // last boundary, no extra cr here!
    body += boundary + "--" + cr;

    //alert( body );

    // finally, the Ajax request
    var isAsync = false;
    var xrequest = new XMLHttpRequest();
    xrequest.open( "POST", actionUrl, isAsync );

    // set request headers
    // please note: chromium needs charset set explicitly.
    //   It will autocomplete if it's missing but it won't work 
    //   (PHP backend only)?
    // also: chromium considers setting Content-length and Connection unsafe
    // this is no problem as all browsers seem to determine this automagically.
    xrequest.setRequestHeader( "Content-Type", "multipart/form-data; charset=UTF-8; boundary="+boundaryString );
    //xrequest.setRequestHeader( "Content-length", body.length );
    //xrequest.setRequestHeader( "Connection", "close" );
    xrequest.send( body );

    return xrequest.responseText;
  }
}
