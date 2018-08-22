var fmc_ui = (function(fmc) {


	val = {
		page: 0,	
		tableToExcel: function(table, name) {

			var uri = 'data:application/vnd.ms-excel;base64,';
			var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>';
			var base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) };
			var format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) };



		    if (!table.nodeType) table = document.getElementById(table)
		    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
		    
			var link = document.createElement('a');
			link.setAttribute('href',uri+base64(format(template, ctx)));
			link.setAttribute('download','sheet.xls');
			link.click();
		    //window.location.href = uri + base64(format(template, ctx))
		},
		refreshData: function() {
			
			fmc.getReqs(function(reqs) {
				fmc.reqs = reqs;
				fmc.getMembers(function(members) {
					fmc.members = members;
					fmc.getDepts(function(depts) {
						fmc.depts = depts;
						document.dispatchEvent(new Event('dataRefreshed'));
					});
				});
			});
			

		},
		renderTable: function() {

			var table = document.getElementById('reqTableBody');
			if (!table) {return;}

			table.innerHTML = "";

			for (i = fmc_ui.page*25; i < (fmc_ui.page+1)*25; i++) {
				var req = fmc.reqs[i];
				if (i > fmc.reqs.length-1) {continue;}

				//build each row
				var row = document.createElement('tr');
				row.req = req;

				//date column
				var date = document.createElement('td');
					var link = document.createElement('a');
						link.href = "details.html?id="+req.upload_unique_id;
						link.innerHTML = new Date(req.upload_timestamp*1000);
						link.innerHTML = link.innerHTML.split("GMT")[0];
					date.appendChild(link);
				row.appendChild(date);

				//name column
				var name = document.createElement('td');
					name.innerHTML = req.user_name_facebook;
				row.appendChild(name);

				//message column
				var message = document.createElement('td');
					message.innerHTML = req.upload_text;
				row.appendChild(message);

				//fmc member column
				var assigned = document.createElement('td');
					var sel = document.createElement('select');
						for (j = 0; j < fmc.members.length; j++) {
							var opt = document.createElement('option');
								opt.value = fmc.members[j].member;
								opt.innerHTML = fmc.members[j].member;
								opt.style.background = "#FFFFFF";
							sel.appendChild(opt);
							if (fmc.members[j].member == req.fmc_member_assigned) {
								sel.selectedIndex = j;
							}
						}
							var opt = document.createElement('option');
								opt.value = "";
								opt.innerHTML = "None";
								opt.style.background = "#EEBBBB";
							sel.appendChild(opt);
							sel.style.width = "90%";
							if ("" == req.fmc_member_assigned) {
								sel.style.background = "#EEBBBB";
								sel.selectedIndex = fmc.members.length;
							}
						sel.addEventListener('change',function(e) {//when it's changed, update its color and send the change to the backend
							newSel = e.target;
							newSel.style.background = newSel.children[newSel.selectedIndex].style.background;
							req = newSel.parentNode.parentNode.req;

							req.fmc_member_assigned = newSel.children[newSel.selectedIndex].value;
							fmc.updateReq(req);
						});
					assigned.appendChild(sel);
				row.appendChild(assigned);

				//upload id column
				var uid = document.createElement('td');
					uid.innerHTML = req.upload_unique_id;
				row.appendChild(uid);

				//Status column
				var status = document.createElement('td');
					var sel = document.createElement('select');
						for (j = 0; j < fmc.statuses.length; j++) {
							var opt = document.createElement('option');
								opt.value = j;
								opt.innerHTML = fmc.statuses[j];
								opt.style.background = fmc.statusColors[j];
								sel.appendChild(opt);
						}
						sel.style.background = fmc.statusColors[req.fmc_project_status];
						sel.style.marginLeft = "-18px";
						sel.style.width = "140%";//bad practice I know, so sue me
						sel.selectedIndex = req.fmc_project_status;
						sel.addEventListener('change',function(e) {//when it's changed, update its color and send the change to the backend
							newSel = e.target;
							newSel.style.background = newSel.children[newSel.selectedIndex].style.background;
							
							req = newSel.parentNode.parentNode.req;

							req.fmc_project_status = newSel.children[newSel.selectedIndex].value;
							fmc.updateReq(req);
						});
					status.appendChild(sel);



				row.appendChild(status);

				//department column
				var dep = document.createElement('td');
					var sel = document.createElement('select');
						for (j = 0; j < fmc.depts.length; j++) {
							var opt = document.createElement('option');
								opt.value = fmc.depts[j].department;
								opt.innerHTML = fmc.depts[j].department;
								opt.style.background = "#FFFFFF";
							sel.appendChild(opt);
							if (fmc.depts[j].department == req.fmc_department) {
								sel.selectedIndex = j;
							}
						}
							var opt = document.createElement('option');
								opt.value = "";
								opt.innerHTML = "None";
								opt.style.background = "#EEBBBB";
							sel.appendChild(opt);
							if ("" == req.fmc_department) {
								sel.style.background = "#EEBBBB";
								sel.selectedIndex = fmc.depts.length;
							}
						sel.addEventListener('change',function(e) {//when it's changed, update its color and send the change to the backend
							newSel = e.target;
							newSel.style.background = newSel.children[newSel.selectedIndex].style.background;
							req = newSel.parentNode.parentNode.req;

							req.fmc_department = newSel.children[newSel.selectedIndex].value;
							fmc.updateReq(req);
						});
					dep.appendChild(sel);
				row.appendChild(dep);

				var image = document.createElement('td');
					if (req.upload_image_address.length > 1) {
						var link = document.createElement('a');
							link.href = req.upload_image_address;
							var pic = document.createElement('img');
								pic.src = req.upload_image_address;
							link.appendChild(pic);
						image.appendChild(link);

					} else if (req.upload_video_address.length > 1) {
						var link = document.createElement('a');
							link.href = req.upload_video_address;
							link.innerHTML = "Video Link";
							link.appendChild(pic);
						image.appendChild(link);
					} else if (req.upload_audio_address.length > 1) {
						var link = document.createElement('a');
							link.href = req.upload_audio_address;
							link.innerHTML = "Audio Link";
							link.appendChild(pic);
						image.appendChild(link);
					} else {
						image.innerHTML = "None";
					}
				row.appendChild(image);


				if (!document.getElementById('onlyUnresolved').checked || req.fmc_project_status == 0) {
					table.appendChild(row);//display the row if the 'only unresolved' button is unchecked, or it is unresolved
				}

			}
		},
		
		nextPage: function(){
			// increment the page
			if (fmc_ui.page*25 < fmc.reqs.length-25) {fmc_ui.page++;}
			// update the table
			fmc_ui.renderTable();
		},
		
		lastPage: function(){
			// decrement the page
			if (fmc_ui.page > 0) {fmc_ui.page--;}
			// update the table
			fmc_ui.renderTable();
		},

		renderSettings: function() {
			memberSel = document.getElementById('memberSel');
			 	for (j = 0; j < fmc.members.length; j++) {
					var opt = document.createElement('option');
						opt.value = fmc.members[j].member_id;
						opt.innerHTML = fmc.members[j].member;
						opt.style.background = "#FFFFFF";
					memberSel.appendChild(opt);
				}


			deptSel = document.getElementById('deptSel');
				for (j = 0; j < fmc.depts.length; j++) {
					var opt = document.createElement('option');
						opt.value = fmc.depts[j].unique_id;
						opt.innerHTML = fmc.depts[j].department;
						opt.style.background = "#FFFFFF";
					deptSel.appendChild(opt);
				}
			
		},

		renderDetails: function() {
			console.log('rendering details...');
			for (i = 0; i < fmc.reqs.length; i++) {
				if (fmc.reqs[i].upload_unique_id == window.location.search.split("=")[1]){
					req = fmc.reqs[i];
				}
			}
			if (!req) {
				return;
				console.log('No request for id=' + window.location.search.split("=")[1]);
			}
			fmc_ui.req = req;

			var sel = document.getElementById('statusSel');
				for (j = 0; j < fmc.statuses.length; j++) {
					var opt = document.createElement('option');
						opt.value = j;
						opt.innerHTML = fmc.statuses[j];
						opt.style.background = fmc.statusColors[j];
					sel.appendChild(opt);
				}
				sel.style.background = fmc.statusColors[req.fmc_project_status];
				//sel.style.marginLeft = "-18px";
				//sel.style.width = "140%";//bad practice I know, so sue me
				sel.selectedIndex = req.fmc_project_status;
				sel.addEventListener('change',function(e) {//when it's changed, update its color and send the change to the backend
					newSel = e.target;
					newSel.style.background = newSel.children[newSel.selectedIndex].style.background;
							
					req = fmc_ui.req;

					req.fmc_project_status = newSel.children[newSel.selectedIndex].value;
					fmc.updateReq(req);
				});

			var sel = document.getElementById('memberSel');
						for (j = 0; j < fmc.members.length; j++) {
							var opt = document.createElement('option');
								opt.value = fmc.members[j].member;
								opt.innerHTML = fmc.members[j].member;
								opt.style.background = "#FFFFFF";
							sel.appendChild(opt);
							if (fmc.members[j].member == req.fmc_member_assigned) {
								sel.selectedIndex = j;
							}
						}
							var opt = document.createElement('option');
								opt.value = "";
								opt.innerHTML = "None";
								opt.style.background = "#EEBBBB";
							sel.appendChild(opt);
							if ("" == req.fmc_member_assigned) {
								sel.style.background = "#EEBBBB";
								sel.selectedIndex = fmc.members.length;
							}
						sel.addEventListener('change',function(e) {//when it's changed, update its color and send the change to the backend
							newSel = e.target;
							newSel.style.background = newSel.children[newSel.selectedIndex].style.background;
							req = fmc_ui.req;

							req.fmc_member_assigned = newSel.children[newSel.selectedIndex].value;
							fmc.updateReq(req);
						});
			
			var sel = document.getElementById('deptSel');
				for (j = 0; j < fmc.depts.length; j++) {
					var opt = document.createElement('option');
						opt.value = fmc.depts[j].department;
						opt.innerHTML = fmc.depts[j].department;
						opt.style.background = "#FFFFFF";
					sel.appendChild(opt);
					if (fmc.depts[j].department == req.fmc_department) {
						sel.selectedIndex = j;
					}
				}
				var opt = document.createElement('option');
					opt.value = "";
					opt.innerHTML = "None";
					opt.style.background = "#EEBBBB";
				sel.appendChild(opt);
				if ("" == req.fmc_department) {
					sel.style.background = "#EEBBBB";
					sel.selectedIndex = fmc.depts.length;
				}
				sel.addEventListener('change',function(e) {//when it's changed, update its color and send the change to the backend
					newSel = e.target;
					newSel.style.background = newSel.children[newSel.selectedIndex].style.background;
					req = fmc_ui.req;

					req.fmc_department = newSel.children[newSel.selectedIndex].value;
					fmc.updateReq(req);
				});

			var noteBody = document.getElementById('noteBody');

			fmc.getNotes(req,function(notes) {
				noteBody = document.getElementById('noteBody');
				noteBody.innerHTML = "";
				for (j = 0; j < notes.length; j++) {
					var note = document.createElement('tr');
						var noteDate = document.createElement('td');
							noteDate.innerHTML = new Date(notes[j].note_timestamp*1000);
							noteDate.innerHTML = noteDate.innerHTML.split("GMT")[0];
						note.appendChild(noteDate);
						var noteData = document.createElement('td');
							noteData.innerHTML = notes[j].note;
						note.appendChild(noteData);
					noteBody.appendChild(note);
				}
			});

			var reqText = document.getElementById('requestText');
				reqText.innerHTML = req.upload_text;

			var originText = document.getElementById('originText');
				originText.innerHTML = fmc.origins[req.upload_origin];

			var dateText = document.getElementById('dateText');
				dateText.innerHTML = new Date(req.upload_timestamp*1000);
				dateText.innerHTML = dateText.innerHTML.split("GMT")[0];

			var nameText = document.getElementById('nameText');
				nameText.innerHTML = req.user_name_facebook;

			var mapBox = document.getElementById('mapBox');
				mapBox.style.width = "555px";
				mapBox.style.height = "400px";
				mapBox.style.display = "block";
				var myLatLng = new google.maps.LatLng(req.user_location_x,req.user_location_y);

				var mapOptions = {
					zoom: 17,
					center: myLatLng,
					disableDefaultUI: false,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
				var map = new google.maps.Map(mapBox, mapOptions);
				var marker = new google.maps.Marker({
					position: myLatLng,
					map: map,
					title: "Request",
					zIndex: i
				});

			var mediaBox = document.getElementById('mediaBox');
				if (req.upload_image_address.length > 1) {
					if (req.upload_image_address.indexOf('.php') > -1) {//check if it's a facebook picture
						link = document.createElement('a');
							link.href = req.upload_image_address;
							link.innerHTML = "Facebook Photo Linked Here.";
						mediaBox.appendChild(link);
					} else {
						image = document.createElement('img');
							img.src = req.upload_image_address;
						mediaBox.appendChild(image);
					}
				} else if (req.upload_video_address.length > 1) {
					link = document.createElement('a');
						link.href = req.upload_video_address;
						link.innerHTML = "Video Linked Here.";
					mediaBox.appendChild(link);
				} else if (req.upload_audio_address.length > 1) {
					link = document.createElement('a');
						link.href = req.upload_audio_address;
						link.innerHTML = "Audio Linked Here.";
					mediaBox.appendChild(link);
				} else {
					mediaBox.innerHTML = "No media attached to this request.";
				}

		}		
	};

document.addEventListener('DOMContentLoaded',function() {
	switch (window.location.pathname){
	case "/admin/index.html":
	case "/admin":
	case "/admin/":
		document.addEventListener('fmcReady',val.refreshData);
		document.addEventListener('dataRefreshed',val.renderTable);
		document.getElementById('newerButton').addEventListener('click',val.lastPage);
		document.getElementById('olderButton').addEventListener('click',val.nextPage);
		document.getElementById('refreshDataButton').addEventListener('click',val.refreshData);
		document.getElementById('onlyUnresolved').addEventListener('click',val.renderTable);
		document.getElementById('exportButton').addEventListener('click',function() {
			val.tableToExcel('reqTable','sheet');
		});
		break;
	case "/admin/settings.html":
		document.addEventListener('fmcReady',val.refreshData);
		document.addEventListener('dataRefreshed',val.renderSettings);
		deptAdd = document.getElementById('addDeptButton');
		deptAdd.addEventListener('click',function() {
			fmc.addDept(new fmc.dept(document.getElementById('newDeptText').value));
			document.getElementById('newDeptText').value = "";
			setTimeout(function() {window.location = window.location.href;},2000);
		});
		deptSel = document.getElementById('deptSel');
		document.getElementById('deleteDeptButton').addEventListener('click',function() {
			var d = new fmc.dept(deptSel.children[deptSel.selectedIndex].innerHTML);
			d.unique_id = deptSel.children[deptSel.selectedIndex].value;
			fmc.removeDept(d);
			setTimeout(function() {window.location = window.location.href;},2000);
		});
		memberAdd = document.getElementById('addMemberButton');
		memberAdd.addEventListener('click',function() {
			fmc.addMember(new fmc.member(document.getElementById('newMemberText').value,Math.floor(Math.random()*1000000+1000)));
			setTimeout(function() {window.location = window.location.href;},2000);
		})
		memberSel = document.getElementById('memberSel');
		document.getElementById('deleteMemberButton').addEventListener('click',function() {
			f = new fmc.member(memberSel.children[memberSel.selectedIndex].innerHTML);
			f.member_id = memberSel.children[memberSel.selectedIndex].value;
			fmc.removeMember(f);
			setTimeout(function() {window.location = window.location.href;},2000);
		});

		break;
	case "/admin/details.html":
		document.addEventListener('fmcReady',val.refreshData);
		document.addEventListener('dataRefreshed',val.renderDetails);
		document.getElementById('noteButton').addEventListener('click',function() {
			fmc.addNote(new fmc.note(document.getElementById('newNote').value,val.req.upload_unique_id));
			setTimeout(fmc_ui.renderDetails,500);
		});
		break;
	}
});
	return val;

})(fmc);