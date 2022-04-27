<html>
	<head>
		<title>Coordinator Engine</title>
		<link rel="icon" type="image/x-icon" href="favicon.ico">
	</head>
	<body>
		<div>
			<img src="logo.png" alt="coordinator-engine-logo" width="126">
			<h1>Coordinator Engine</h1>
			<p id="status">Checking status...</p>
		</div>
	</body>
</html>
<script type="text/javascript">
  var xhr=new XMLHttpRequest();
  xhr.onreadystatechange=function(){
    console.log(xhr);
    if(xhr.readyState===4){
      //should be true
      try{
      	var responseParsed=JSON.parse(xhr.responseText);
      	if(responseParsed.data.response=='pong'){
          var online=true;
        }else{
          var online=false;
        }
			}catch(e){
        return false;
      }
    }
    if(online){
      document.getElementById('status').innerHTML="<span class='dot green'></span> Service available";
		}else{
      document.getElementById('status').innerHTML="<span class='dot red'></span> Service not available";
		}
  }
  function check(){
    console.log('check');
		document.getElementById('status').innerHTML="<span class='dot orange'></span> Checking status...";
    xhr.open('GET','http://coordinator-engine.test/Utilities/Ping');
    xhr.send();
	}
  window.onload=function(){check();}
  setInterval(function(){check();},1000*60);
</script>
<style>
	div{width:270px;margin:0 auto;padding-top:99px;text-align:center;}
	h1{font-family:sans-serif;font-size:27px;}
	p{font-family:sans-serif;font-size:18px;}
	.dot{height:13px;width:13px;border-radius:50%;display:inline-block;}
	.green{background-color:green;}
	.orange{background-color:orange;}
	.red{background-color:red;}
</style>