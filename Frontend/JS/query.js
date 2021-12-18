const QUERY_DATA = "http://api.ensolvers.local/";
function QueryData(params,callback){
    var uri = params.uri ?? null;
    var type = params.type ?? null;
    var data = params.data ?? null;
    if(uri.trim() == ""){ return; }
    type = type.toUpperCase();
    if(["GET","POST","PUT"].indexOf(type) === -1){ type = "POST"; }

    uri = uri.replace(/^[\/]+/,"");
    uri = uri.replace(/[\/]+$/,"");
    if(uri.trim() == ""){ return; }
    console.log(QUERY_DATA+uri);
    var headData = {
        method: type, // *GET, POST, PUT, DELETE, etc.
        mode: 'cors', // no-cors, *cors, same-origin
        cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
        credentials: 'same-origin', // include, *same-origin, omit
        headers: {
            'Content-Type': 'application/json'
        // 'Content-Type': 'application/x-www-form-urlencoded',
        },
        redirect: 'follow', // manual, *follow, error
        referrerPolicy: 'no-referrer' // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
    }
    if(data && (data.length > 0 || Object.keys(data).length > 0)){
        if(type == 'GET'){
            var i = 0;
            simbol = "?";
            for(d in data){
                if(i > 0){
                    simbol = "&";
                }
                uri +=simbol+d+"="+data[d];
                i++;
            }
        }else{
            headData['body'] = JSON.stringify(data); // body data type must match "Content-Type" header
        }
    }

    fetch(QUERY_DATA+uri,headData).then(data => {
        data.text().then((text)=>{ 
            var resultData = JSON.parse(text);
            var returnData = null;
            if(resultData && resultData.data){
                returnData = resultData.data;
            }
            if(typeof callback === 'function'){
                callback(data.status,returnData);
            }
         });
    }).catch(error => {
        console.error('Error:', error);
        if(typeof callback === 'function'){
            callback(null,null);
        }
    })
}

function MakeQuery(uri,headData){
    return new Promise((res)=>{
        
    });
}