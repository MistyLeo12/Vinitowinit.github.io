//Fix My Campus 2.5 Javascript Recode
//Entirely by Jason "Toolbox" Oettinger
//Property of Duke Innovation Co-Lab
//Fixes... a lot of things

//emits 'fmcReady' on initialization

var FB;
var fmc = (function() {
	val = {
		error: "none",
		statuses: ["Unresolved","In Progress","Completed","Abandoned","Ignored"],
		statusColors: ["#EEBBBB","#EEEEBB","#BBEEBB","#BBBBBB","#999999"],
		origins: ['','','App','Facebook'],
		mostRecent: 0,
		//-----------------------------------------------------------------------------------------------
		//Needs to be called before any other API calls.
		initFMC: function() {
			//load up Facebook SDK
			(function(d, s, id) {
				fbStart = new Date().getTime();
				console.log('Loading SDK Asynchronously');
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_US/sdk.js";
				fjs.parentNode.insertBefore(js, fjs);
				fbStop = new Date().getTime();
				fbT = fbStop - fbStart;
			}(document, 'script', 'facebook-jssdk'));

			window.fbAsyncInit = function(){
				console.log('Initializing Facebook...');
				FB.init({
		  			appId      : '670916569623587',
					status     : true,
		  			cookie     : true,  // enable cookies to allow the server to access 
							  // the session
		  			xfbml      : true,  // parse social plugins on this page
		  			version    : 'v2.1' // use version 2.1
				});
				console.log('Facebook Initialized');
				
				FB.getLoginStatus(function(response){
					console.log('FB Login Status: '+JSON.stringify(response)); 
					if(response.status === 'connected') {
						console.log('User Access Token: '+FB.getAccessToken());
						fmc.get("php/fbAdminCheck.php",function(res) {
							

							if (res.status != 200 && window.location.href.indexOf("login.php")<0) {
								window.location = "login.php";
								return;
							}
							if (res.status == 200 && window.location.href.indexOf("login.php")>-1) {
								window.location = "/admin";
								return;
							}

							fmc.updateDB();
							document.dispatchEvent(new Event('fmcReady'));
						});
					} else {
						if (window.location.href.indexOf("login.php")<0) {
							console.log('Redirecting to login page...');
							window.location = "login.php";
							return;
						}
						/*FB.login(function(response) {
							console.log('FB Login Status: '+JSON.stringify(response));
							fmc.get("php/fbAdminCheck.php",function(res) {
								if (res.status != 200) {
									window.location = "login.php";
								}
								fmc.updateDB();
								document.dispatchEvent(new Event('fmcReady'));
							});
						}, {
							scope: 'public_profile,email,publish_actions,user_groups',
							return_scopes: true
						});*/
					}
					
				});


			};
		},//---------------------------------------------------------------------------------------------
		updateDB: function() {
			console.log("Updating DB, adding new FB posts...");
			timer = Date.now();
			fmc.get("php/getReqs.php?sort=recent&limit=1",function(dbresponse) {
				if (dbresponse.status != 200) {//check for errors
					console.log("Error in updateDB(1).");
					return false;
				}
				issues = JSON.parse(dbresponse.response);//extract the actual object
				if (issues != null && issues.length > 0) {
					fmc.mostRecent = issues[0].upload_timestamp;//update mostRecent
				}
				FB.api("/v2.2/347519535355326/feed?since="+fmc.mostRecent+"&limit=10000",function(fbresponse) {
					data = fbresponse.data;//get the facebook updates since mostRecent
					for (i = 0; i < data.length; i++) {
						timestamp = new Date(data[i].created_time).getTime()/1000;
						if (timestamp>fmc.mostRecent) {//check only for posts that were CREATED since mostRecent, not just updated
							fmc.addReq(data[i]);
						}
					}
					console.log("Took "+(Date.now()-timer)+" milliseconds. (updateDB)");
				}, {access_token: FB.getAccessToken()});
			});
		},//---------------------------------------------------------------------------------------------
		getReqs: function(callback) {
			console.log("Getting list of Requests...");
			timer = Date.now();
			fmc.get("php/getReqs.php?sort=recent",function(dbresponse) {
				console.log("Took "+(Date.now()-timer)+" milliseconds. (getReqs)");
				if (dbresponse.status != 200) {//check for errors
					console.log("Error in getReqs(1).");
					return false;
				}
				reqs = JSON.parse(dbresponse.response);//extract the actual object
				callback(reqs);
			});
		},//---------------------------------------------------------------------------------------------
		addReq: function(facebook_formatted_request) {
			ffr = facebook_formatted_request;
			console.log("Adding request..."+JSON.stringify(ffr));
			timer = Date.now();
			fmc.post("php/addReq.php",function(dbresponse) {
				console.log("Took "+(Date.now()-timer)+" milliseconds. (addReq)");
				if (dbresponse.status != 200) {//check for errors
					console.log("Error in addReq(1).");
					return false;
				}
			},"req="+encodeURIComponent(JSON.stringify(ffr)));
			return true;
		},//---------------------------------------------------------------------------------------------
		updateReq: function(request) {
			console.log("Updating Request..."+JSON.stringify(request));
			timer = Date.now();
			fmc.post("php/updateReq.php",function(dbresponse) {
				console.log("Took "+(Date.now()-timer)+" milliseconds. (updateReq)");
				if (dbresponse.status != 200) {//check for errors
					console.log("Error in updateReq(1).");
					return false;
				}
			},"req="+encodeURIComponent(JSON.stringify(request)));
			return true;
		},//---------------------------------------------------------------------------------------------
		removeReq: function(req) {
			console.log("Removing Request "+req+"...");
			timer = Date.now();
			fmc.post("php/removeReq.php",function(dbresponse) {
				console.log("Took "+(Date.now()-timer)+" milliseconds. (removeReq)");
				if (dbresponse.status != 200) {//check for errors
					console.log("Error in removeReq(1).");
					return false;
				}
			},"req="+encodeURIComponent(JSON.stringify(req)));
			return true;
		},//---------------------------------------------------------------------------------------------
		getMembers: function(callback) {
			console.log("Getting list of Members...");
			timer = Date.now();
			fmc.get("php/getMembers.php",function(dbresponse) {
				console.log("Took "+(Date.now()-timer)+" milliseconds. (getMembers)");
				if (dbresponse.status != 200) {//check for errors
					console.log("Error in getMembers(1).");
					return false;
				}
				members = JSON.parse(dbresponse.response);//extract the actual object
				callback(members);
			});
		},//---------------------------------------------------------------------------------------------
		addMember: function(member) {
			console.log("Adding Member..."+JSON.stringify(member));
			timer = Date.now();
			fmc.post("php/addMember.php",function(dbresponse) {
				console.log("Took "+(Date.now()-timer)+" milliseconds. (addMember)");
				if (dbresponse.status != 200) {//check for errors
					console.log("Error in addMember(1).");
					return false;
				}
			},"member="+encodeURIComponent(JSON.stringify(member)));
			return true;
		},//---------------------------------------------------------------------------------------------
		removeMember: function(member) {
			console.log("Removing Member "+JSON.stringify(member)+"...");
			timer = Date.now();
			fmc.post("php/removeMember.php",function(dbresponse) {
				console.log("Took "+(Date.now()-timer)+" milliseconds. (removeMember)");
				if (dbresponse.status != 200) {//check for errors
					console.log("Error in removeMember(1).");
					return false;
				}
			},"member="+encodeURIComponent(JSON.stringify(member)));
			return true;
		},//---------------------------------------------------------------------------------------------
		getDepts: function(callback) {
			console.log("Getting list of Departments...");
			timer = Date.now();
			fmc.get("php/getDepts.php",function(dbresponse) {
				console.log("Took "+(Date.now()-timer)+" milliseconds. (getDepts)");
				if (dbresponse.status != 200) {//check for errors
					console.log("Error in getDepts(1).");
					return false;
				}
				depts = JSON.parse(dbresponse.response);//extract the actual object
				callback(depts);
			});
		},//---------------------------------------------------------------------------------------------
		addDept: function(dept) {
			console.log("Adding Dept..."+JSON.stringify(dept));
			timer = Date.now();
			fmc.post("php/addDept.php",function(dbresponse) {
				console.log("Took "+(Date.now()-timer)+" milliseconds. (addDept)");
				if (dbresponse.status != 200) {//check for errors
					console.log("Error in addDept(1).");
					return false;
				}
			},"dept="+encodeURIComponent(JSON.stringify(dept)));
			return true;
		},//---------------------------------------------------------------------------------------------
		removeDept: function(dept) {
			console.log("Removing Dept "+JSON.stringify(dept)+"...");
			timer = Date.now();
			fmc.post("php/removeDept.php",function(dbresponse) {
				console.log("Took "+(Date.now()-timer)+" milliseconds. (removeDept)");
				if (dbresponse.status != 200) {//check for errors
					console.log("Error inremoveDept(1).");
					return false;
				}
			},"dept="+encodeURIComponent(JSON.stringify(dept)));
			return true;
		},//---------------------------------------------------------------------------------------------
		getNotes: function(req,callback) {
			console.log("Getting list of Notes...");
			timer = Date.now();
			fmc.get("php/getNotes.php?id="+req.upload_unique_id,function(dbresponse) {
				console.log("Took "+(Date.now()-timer)+" milliseconds. (getDepts)");
				if (dbresponse.status != 200) {//check for errors
					console.log("Error in getNotes(1).");
					return false;
				}
				notes = JSON.parse(dbresponse.response);//extract the actual object
				callback(notes);
			});
		},//---------------------------------------------------------------------------------------------
		addNote: function(newNote) {
			console.log("Adding Note..." + JSON.stringify(newNote));
			timer = Date.now();
			fmc.post("php/addNote.php",function(dbresponse) {
				console.log("Took "+(Date.now()-timer)+" milliseconds. (addDept)");
				if (dbresponse.status != 200) {//check for errors
					console.log("Error in addNote(1).");
					console.log(dbresponse);
					return false;
				}
			},"note="+encodeURIComponent(JSON.stringify(newNote)));
			return true;
		},//---------------------------------------------------------------------------------------------
		get: function(URL,callback) {return fmc.http(URL,callback,'GET',0);},
		post: function(URL,callback,data) {return fmc.http(URL,callback,'POST',data);},
		http: function(URL,callback,request,data) {
			var httpRequest;
			if (window.XMLHttpRequest) { // Mozilla, Safari, IE7+ ...
    			httpRequest = new XMLHttpRequest();
			} else if (window.ActiveXObject) { // IE 6 and older
    			 try {
        			httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
      			} catch (e) {
        			try {
          				httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
        			} catch (e) {}
      			}
			}
			if (!httpRequest) {
				error = "Somehow there's no httpRequest in this browser... :(";
				return false;
			}
			httpRequest.onreadystatechange = function(e) {
				if (e.target.readyState === 4) {
					callback(e.target);
				}
			};
			
			if (data===0) {
				if (URL.indexOf('?') > -1) {
					URL += "&";
				} else {
					URL += "?";
				}
				httpRequest.open(request,URL+"access_token="+FB.getAccessToken());
				httpRequest.send();
			} else {
				httpRequest.open(request,URL);
				httpRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				httpRequest.send(data+"&access_token="+FB.getAccessToken());
			}
		},
		//-------------------------------------------
		//OBJECT PROTOTYPES
		//-------------------------------------------
		dept: function() {
			if (arguments.length>0) {
				this.department = arguments[0];
			} else {
				this.department = "";
			}
			this.is_deleted = "0";
			this.unique_id = "";
		},
		member: function() {
			this.is_deleted = "0";
			this.member_id = "";
			this.member = "";
			if (arguments.length>0) {
				this.member = arguments[0];
			}
			if (arguments.length>1) {
				this.member_id = arguments[1];
			}
		},
		note: function() {
			this.note = "";
			this.upload_unique_id = "";
			this.note_timestamp = Math.floor((new Date).getTime()/1000);
			if (arguments.length>0) {
				this.note = arguments[0];
			}
			if (arguments.length>1) {
				this.upload_unique_id = arguments[1];
			}
		}
	};


	//initialize FMC
	val.initFMC();

	return val;

	
})(FB);