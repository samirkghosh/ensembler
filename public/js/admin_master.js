jQuery(function ($){
    var Admin = {
        init: function (){
        	jQuery("body").on('click','.category_delete',this.HandleCateDelete); // this function for delete category
            jQuery("body").on('click', '.subcategory_delete', this.HandleSubCateDelete);//THIS FUNCTION FOR DELETING SUBCATEGORY
			jQuery("body").on('click', '.province_delete', this.HandleProvinceDelete); // this function for deleting province 
			jQuery("body").on('click', '.village_delete', this.HandleVillageDelete); //this function for deleting the village  from web_Village
			jQuery("body").on('click', '.mail_delete', this.HandleMailDelete); // this function for deleting the mail from web_mailformats
			jQuery("body").on('click', '.sms_delete', this.HandleSmsDelete);  //this function for deleting the sms from web_smsformats
			jQuery("body").on('click', '.project_delete', this.HandleProjectDelete); // this function is for deleting the project from web_projects
			jQuery("body").on('click', '.status_delete', this.HandleStatusDelete );  //function to delete the status from web_tickestatus
			jQuery("body").on('click', '.base_delete', this.HandleBaseDelete );  //fucntion to delete the knowledge base from tbl_mst_faq
			jQuery("body").on('click', '.disposition_delete', this.HandleDispositionDelete ); //fucntion to delete the Disposition
			jQuery("body").on('click', '.delete_bulletin', this.HandleBulletinDelete ); //fucntion to delete the bulletin
			jQuery("body").on('click', '.bulk_delete', this.HandleBulkDelete);  //this function for deleting the bulk sms email template
			jQuery("body").on('click','#submitclkcity',this.HandleProvinceSubmit);  //function for handling request of province submit and update 
			jQuery("body").on('click','#submitclkvillage',this.HandleVillageSubmit);  //function for handling request of village submit and update 
			jQuery("body").on('click','#submitclksub',this.HandleSubcategorySubmit); // function for handling request of subcategory submit and update 
			jQuery("body").on('click','#submitclkmail',this.HandleMailSubmit);  //function for handling request of mail format submit and update 
			jQuery("body").on('click','#submitclksms',this.HandleSmsSubmit); //fucntion for handling request of sms format submit and update
			jQuery("body").on('click','#submitclkproject',this.HandleProjectSubmit);  //function for handling request of  project submit and update 
			jQuery("body").on('click','#submitclkstatus',this.HandleStatusSubmit); //function for handling request of status submit and update 
			jQuery("body").on('click','#submitclkbase',this.HandleBaseSubmit);  //function for handling request of Knowledge Base  submit and update 
			jQuery("body").on('click','#submitclkbulk',this.HandleBulkSubmit);  //function for handling request of Knowledge Base  submit and update 
			jQuery("body").on('click','#submitclkescalation',this.HandleEscalationSubmit); //function for handlong request of updating  escalation status 
			jQuery("body").on('click','#submitclksmtp',this.HandleSMTPSubmit); //FUNCTION  for  handling  update request of smtp 
			jQuery("body").on('click','#submitclkimap',this.HandleIMAPSubmit); //FUNCTION  for  handling  update request of imap
			jQuery("body").on('click','#submitclkdisposition',this.HandleDispositionSubmit); //function to  update or insert in disposition table
			jQuery("body").on('click','#submitclkcallbacks',this.HandleCallbacksSubmit); //function to update   callback 
			jQuery("body").on('click','#update_sent_mail',this.HandleSentMailSubmit); //function to update tbl connection record for multiple case aert
			jQuery("body").on('click','#submitclk',this.HandleCategorySubmit);
			jQuery("body").on('click', '.formsubmitnews', this.validateForm);  //function to handle the request of  department assign 
			jQuery("body").on('click', '#submitadhoc', this.HandleAdhocMail);  //function to handle the updation of adhoc email id
			jQuery("body").on('click', '.webchat_delete', this.HandlewebchatDelete); 
			jQuery("body").on('click','#submitclkwebtemp',this.HandlewebchatSubmit); 
			jQuery("body").on('click', '.whatsapp_delete', this.HandlewhatsappDelete); 
			jQuery("body").on('click','#submitclkwhatstemp',this.HandlewhatsappSubmit);
			jQuery("body").on('click','#submitclkspam',this.HandleSpamSubmit);  
			jQuery("body").on('click', '.spam_mail_delete', this.HandleSpamMailDelete); 
       		

       		$('#type').select2({});
			$('#level3_users').select2({});
			$('#level2_users').select2({});
			$('#level1_users').select2({});
       	},
		   HandleCateDelete: function () {
			if (confirm("Are you sure to delete?")) {
				var id = $(this).data('id');
				console.log(id);
				SecureAjax.post('admin/web_admin_function.php',
					{ 'id': id, 'action': 'category_delete' },
					function(response) {
						if (response.trim().toLowerCase() === 'success') {
							$('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Category Deleted Successfully</div>');
							setTimeout(function () {
								location.reload();
							}, 2000);
						} else {
							$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error deleting Category</div>');
						}
					},
					function(error) {
						console.error('Error deleting category:', error);
						$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to delete Category</div>');
					}
				);
			}
		},
		HandleSubCateDelete: function () {
			if (confirm("Are you sure to delete this subcategory?")) {
				var id = $(this).data('id');
			    console.log(id);
				SecureAjax.post('admin/web_admin_function.php',
					{ 'id': id, 'action': 'subcategory_delete' },
					function(response) {
						if (response.trim().toLowerCase() === 'success') {
							$('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Subcategory Deleted Successfully</div>');
							setTimeout(function () {
								location.reload();
							}, 2000); 
						} else {
							$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error deleting Sub Category </div>');
						}
					},
					function(error) {
						console.error('Error deleting subcategory:', error);
						$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to delete Sub Category</div>');
					}
				);
			}
		},
		HandleBulkDelete: function () {
			if (confirm("Are you sure to delete this Template?")) {
				var id = $(this).data('id');
			    console.log(id);
				SecureAjax.post('admin/web_admin_function.php',
					{ 'id': id, 'action': 'bulk_delete' },
					function(response) {
						if (response.trim().toLowerCase() === 'success') {
							$('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Template Deleted Successfully</div>');
							setTimeout(function () {
								location.reload();
							}, 2000); 
						} else {
							$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error deleting Template</div>');
						}
					},
					function(error) {
						console.error('Error deleting template:', error);
						$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to delete Template</div>');
					}
				);
			}
		},
		HandleBulletinDelete: function () {
			if (confirm("Are you sure to delete this Bulletin?")) {
				var id = $(this).data('id');
			    console.log(id);
				SecureAjax.post('admin/web_admin_function.php',
					{ 'id': id, 'action': 'delete_bulletin' },
					function(response) {
						if (response.trim().toLowerCase() === 'success') {
							$('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Bulletin Deleted Successfully</div>');
							setTimeout(function () {
								location.reload();
							}, 2000); 
						} else {
							$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error deleting Bulletin </div>');
						}
					},
					function(error) {
						console.error('Error deleting bulletin:', error);
						$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to delete Bulletin</div>');
					}
				);
			}
		},
		HandleProvinceDelete: function () {
			if (confirm("Are you sure to delete this province?")) {
				var id = $(this).data('id');
				console.log(id);
		
				SecureAjax.post('admin/web_admin_function.php',
					{ 'id': id, 'action': 'province_delete' },
					function(response) {
						if (response.trim().toLowerCase() === 'success') {
							$('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Province Deleted Successfully</div>');
							setTimeout(function () {
								location.reload();
							}, 2000);
						} else {
							$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error deleting province</div>');
						}
					},
					function(error) {
						console.error('Error deleting province:', error);
						$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to delete Province</div>');
					}
				);
			}
		},
		HandleVillageDelete: function () {
			if (confirm("Are you sure to delete?")) {
				var id = $(this).data('id');
				console.log(id);
				SecureAjax.post('admin/web_admin_function.php',
					{ 'id': id, 'action': 'village_delete' },
					function(response) {
						if (response.trim().toLowerCase() === 'success')  {
							$('#success').html('<div class="alert alert-success alert-dismissible" role="alert">District Deleted Successfully</div>');
							setTimeout(function () {
								location.reload();
							}, 2000); 
						} else {
				
							$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error Deleting District</div>');
						}
					},
					function(error) {
						console.error('Error deleting district:', error);
						$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to delete District</div>');
					}
				);
			}
		},
		HandleMailDelete: function () {
			if (confirm("Are you sure to delete?")) {
				var id = $(this).data('id');
				console.log(id);
				SecureAjax.post('admin/web_admin_function.php',
					{ 'id': id, 'action': 'mail_delete' },
					function(response) {
						if (response.trim().toLowerCase() === 'success') {
							$('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Mail Deleted Successfully</div>');
							setTimeout(function () {
								location.reload();
							}, 2000);
						} else {
							
							$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error Deleting Mail </div>');
						}
					},
					function(error) {
						console.error('Error deleting mail:', error);
						$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to delete Mail</div>');
					}
				);
			}
		},
		HandleSmsDelete: function () {
			if (confirm("Are you sure to delete?")) {
				var id = $(this).data('id');
				console.log(id);
				SecureAjax.post('admin/web_admin_function.php',
					{ 'id': id, 'action': 'sms_delete' },
					function(response) {
						if (response.trim().toLowerCase() === 'success') {
							$('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Mail Format Deleted Successfully</div>');
							setTimeout(function () {
								location.reload();
							}, 2000);
						} else {
							$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error Deleting Mail Format</div>');
						}
					},
					function(error) {
						console.error('Error deleting SMS:', error);
						$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to delete Mail Format</div>');
					}
				);
			}
		},HandlewebchatDelete: function () {
			if (confirm("Are you sure to delete?")) {
				var id = $(this).data('id');
				console.log(id);
				SecureAjax.post('admin/web_admin_function.php',
					{ 'id': id, 'action': 'webchat_delete' },
					function(response) {
						if (response.trim().toLowerCase() === 'success') {
							$('#success').html('<div class="alert alert-success alert-dismissible" role="alert">WebChat Template Deleted Successfully</div>');
							setTimeout(function () {
								location.reload();
							}, 2000);
						} else {
							$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error Deleting WebChat Template</div>');
						}
					},
					function(error) {
						console.error('Error deleting webchat:', error);
						$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to delete WebChat Template</div>');
					}
				);
			}
		},
		HandleSpamMailDelete: function () {
            if (confirm("Are you sure to delete?")) {
                var id = $(this).data('id');
                console.log("Deleting ID:", id);
                SecureAjax.post('admin/web_admin_function.php',
                    { id: id, action: 'spam_mail_delete' },
                    function(response) {
                        if (response.trim().toLowerCase() === 'success') {
                            $('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Spam Mail Deleted Successfully</div>');
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        } else {
                            $('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error Deleting Spam Mail</div>');
                        }
                    },
                    function(error) {
                        console.error('Error deleting spam mail:', error);
                        $('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to delete Spam Mail</div>');
                    }
                );
            }
        },
		HandlewhatsappDelete: function () {
			if (confirm("Are you sure to delete?")) {
				var id = $(this).data('id');
				console.log(id);
				SecureAjax.post('admin/web_admin_function.php',
					{ 'id': id, 'action': 'whatsapp_delete' },
					function(response) {
						if (response.trim().toLowerCase() === 'success') {
							$('#success').html('<div class="alert alert-success alert-dismissible" role="alert">WhatsApp Template Deleted Successfully</div>');
							setTimeout(function () {
								location.reload();
							}, 2000);
						} else {
							$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error Deleting WhatsApp Template</div>');
						}
					},
					function(error) {
						console.error('Error deleting whatsapp:', error);
						$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to delete WhatsApp Template</div>');
					}
				);
			}
		},
		HandleProjectDelete: function (e) {
			if (confirm("Are you sure to delete?")) {
				var id = $(this).data('id');
				console.log(id);
				SecureAjax.post('admin/web_admin_function.php',
					{ 'id': id, 'action': 'project_delete' },
					function(response) {
						if (response.trim().toLowerCase() === 'success') { 
							$('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Project Deleted Successfully</div>');
							setTimeout(function () {
								location.reload();
							}, 2000);
						} else {
							$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error Deleting Project </div>');
						}
					},
					function(error) {
						console.error('Error deleting project:', error);
						$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to delete Project</div>');
					}
				);
			}
		},
		HandleStatusDelete: function () {
			if (confirm("Are you sure to delete?")) {
				var id = parseInt($(this).data('id'));
				console.log(id);
				SecureAjax.post('admin/web_admin_function.php',
					{ 'id': id, 'action': 'status_delete' },
					function(response) {
						if (response.trim() === 'success') { 
							$('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Category Delete Successfully</div>');
							setTimeout(function () {
								location.reload();
							}, 2000);
						} else {
							alert('Error updating status: ' + response);
						}
					},
					function(error) {
						console.error('Error updating status:', error);
						alert('Error: Unable to update status');
					}
				);
			}
		},
		HandleBaseDelete: function () {
			if (confirm("Are you sure to delete this FAQ?")) {
				var i_id = $(this).data('i_id');
				console.log('i_id:', i_id);
				SecureAjax.post('admin/web_admin_function.php',
					{ 'i_id': i_id, 'action': 'base_delete' },
					function(response) {
						console.log('AJAX Response:', response);
						if (response.trim().toLowerCase() === 'success') {
							$('#success').html('<div class="alert alert-success alert-dismissible" role="alert">FAQ Deleted Successfully</div>');
							setTimeout(function () {
								location.reload(true);
							}, 2000); 
						} else {
							$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Deletion Failed: ' + response + '</div>');
						}
					},
					function(error) {
						console.error('Error deleting FAQ:', error);
						$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to delete FAQ</div>');
					}
				);
			}
		},
		HandleDispositionDelete: function () {
			if (confirm("Are you sure to delete this Disposition?")) {
				var id = $(this).data('id');
			    console.log(id);
				SecureAjax.post('admin/web_admin_function.php',
					{ 'id': id, 'action': 'disposition_delete' },
					function(response) {
						if (response.trim().toLowerCase() === 'success') {
							$('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Disposition Deleted Successfully</div>');
							setTimeout(function () {
								location.reload();
							}, 2000); 
						} else {
							$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error deleting Sub Category </div>');
						}
					},
					function(error) {
						console.error('Error deleting disposition:', error);
						$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to delete Disposition</div>');
					}
				);
			}
		},
		HandleCategorySubmit: function(e) {
			e.preventDefault();
			var complaint_type = $('#type').val(); // Get the complaint type value
			if (complaint_type == '0') {
				alert("Please Select Complaint Type!");
				$('#type').focus();
				return false;
			}
		
			var category = $('#category').val();
			if (!category.trim()) {
				alert("Please Enter a Category!");
				$('#category').focus();
				return false;
			} else {
				var V_Description = $('#V_Description').val();
				var id = $('#id').val();
		
				// Log the data being sent to web_admin_function.php
				console.log('Data sent to web_admin_function.php:', {
					'V_Description': V_Description,
					'category': category.trim(),
					'complaint_type': complaint_type, // Add complaint type to the data object
					'id': id,
					'action': 'Submit_category'
				});
		
				SecureAjax.post('admin/web_admin_function.php',
					{
						'V_Description': V_Description,
						'category': category.trim(),
						'complaint_type': complaint_type, // Include complaint type in the data
						'id': id,
						'action': 'Submit_category'
					},
					function(data) {
						$('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Category ' + (id !== '' ? 'updated' : 'inserted') + ' successfully</div>');
						setTimeout(function () {
							var encodedToken = btoa('view_category');
							window.location.href = "admin_index.php?action=view_category&token=" + encodeURIComponent(encodedToken);
						}, 2000); 
					},
					function(error) {
						console.error('Error submitting category:', error);
						$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to submit category</div>');
					}
				);
			}
		},
		HandleSubcategorySubmit: function (e) {
			e.preventDefault();
			var subcategory = $('#subcategory').val();
			if (!subcategory) {
				alert("Please enter Sub Category!");
				$('#subcategory').focus();
				return false;
			}
			var category = $('#category').val();
			if (!category) {
				alert("Please choose a Category!");
				$('#category').focus();
				return false;
			}
			var time_hours = $('#time_hours').val();
			if (!time_hours || isNaN(time_hours) || time_hours <= 0) {
				alert("Please enter a valid Escalation Time in hours");
				$('#time_hours').focus();
				return false;
			}
			var level1_users = $('#level1_users').val();
			if (!level1_users || isNaN(level1_users) || level1_users <= 0) {
				alert("Please select level 1 user");
				$('#level1_users').focus();
				return false;
			}
			var V_Description = $('#V_Description').val();
			var second_resolution = $('#second_resolution').val();
			var third_resolution = $('#third_resolution').val();
			var level1_users = $('#level1_users').val();
			var level2_users = $('#level2_users').val();
			var level3_users = $('#level3_users').val();
			var id = $('#id').val();
			SecureAjax.post('admin/web_admin_function.php',
				{
					'category': category,
					'subcategory': subcategory,
					'time_hours': time_hours,
					'V_Description': V_Description,
					'second_resolution': second_resolution,
					'third_resolution': third_resolution,
					'level1_users': level1_users,
					'level2_users': level2_users,
					'level3_users': level3_users,
					'id': id,
					'action': 'Submit_subcategory'
				},
				function (data) {
					$('#success').html('<div class="alert alert-success alert-dismissible" role="alert">' + data + '</div>');
					setTimeout(function () {
						var encodedToken = btoa('view_subcategory');
						window.location.href = "admin_index.php?action=view_subcategory&token=" + encodeURIComponent(encodedToken);
					}, 2000); 
				},
				function(error) {
					console.error('Error submitting subcategory:', error);
					$('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to submit subcategory</div>');
				}
			);
		},
		HandleProvinceSubmit: function (e) {
			e.preventDefault();
			var id = jQuery("#id").val();
			var city = jQuery("#city").val();
			var action = (id !== '') ? 'update_province' : 'submit_province';
			var regex = /^[a-zA-Z\s]+$/;

			if (city === '') {
				// Show alert for empty province
				alert('Please enter Province Name');
				return; // Stop further execution
			}

			// Check if the city field is empty or contains invalid characters
			if (!regex.test(city)) {
				// Show alert for empty or invalid province
				alert('Please enter a valid Province Name');
				return; // Stop further execution
			}
			
			SecureAjax.post('admin/web_admin_function.php',
				{ 'id': id, 'city': city, 'action': action },
				function (response) {
					if (response.trim().toLowerCase() === 'success') {
						jQuery('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Province ' + (id !== '' ? 'updated' : 'inserted') + ' successfully</div>');
						setTimeout(function () {
							var encodedToken = btoa('view_province');
							window.location.href = "admin_index.php?action=view_province&token=" + encodeURIComponent(encodedToken);
						}, 2000);
					} else {
						jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + response + '</div>');
					}
				},
				function(error) {
					console.error('Error submitting province:', error);
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to submit province</div>');
				}
			);
		},
		HandleSentMailSubmit: function(e){
			e.preventDefault();
		
			const sent_mail = $("#sent_mail").val();
			const case_count = $("#case_count").val();
			const status_active = $("input[name='status_active']:checked").val(); // Get the selected radio button value

			SecureAjax.post('admin/web_admin_function.php',
				{
					action: 'update_sent_mail',
					sent_mail: sent_mail,
					case_count: case_count,
					status_active: status_active 
				},
				function (response) {
					alert(response);
					$("#message-box").html(response);
					setTimeout(() => {
						location.reload();
					}, 2000);
				},
				function(error) {
					alert("Failed to update. Please try again."); 
					$("#message-box").html("Failed to update. Please try again.");
				}
			);
		},		
		HandleVillageSubmit: function (e) {
			e.preventDefault();
			
			var district = jQuery("#district").val();
			if (!district) {
				alert('Please Select Province!');
				return;
			}
			var village = jQuery("#village").val();
		
			var villageRegex = /^[a-zA-Z\s]+$/;
			if (!village) {
				alert('Village cannot be empty!');
				return;
			}
			if (!villageRegex.test(village)) {
				alert('Please enter valid Village Name');
				return;
			}
			var V_Description = jQuery("#V_Description").val();
			SecureAjax.post('admin/web_admin_function.php',
				{
					'id': jQuery("#id").val(),
					'district': district,
					'village': village,
					'V_Description': V_Description,
					'action': 'submit_village'
				},
				function (response) {
					try {
						var jsonResponse = JSON.parse(response);
						if (jsonResponse.error) {
							jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + jsonResponse.error_msg + '</div>');
						} else if (jsonResponse.message === 'success') {
							jQuery('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Village ' + (village !== '' ? 'updated' : 'inserted') + ' successfully</div>');
							setTimeout(function () {
								var encodedToken = btoa('view_village');
								window.location.href = "admin_index.php?action=view_village&token=" + encodeURIComponent(encodedToken);
							}, 2000);
						} else {
							jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Unexpected Error: ' + response + '</div>');
						}
					} catch (e) {
						jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Parse Error: ' + response + '</div>');
					}
				},
				function(error) {
					console.error('Error submitting village:', error);
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Unable to submit village</div>');
				}
			);
		},
		HandleMailSubmit: function (e) {
			e.preventDefault();
			
			var template_name = $("#template_name").val();
			var type = $("#type").val();
			var subject = $("#subject").val();
			var greeting = $("#greeting").val();
			var body = $("#body").val();
			var description = $("#description").val();
			var signature = $("#signature").val();
			var expiry = $("#expiry").val();

			// Check if template_name is empty
			if (template_name === '') {
				alert('Please enter a template name.');
				return; // Stop further execution
			}
		
			// Check if type is empty
			if (type === '') {
				alert('Please enter a mail type.');
				return; // Stop further execution
			}
		
			// Check if subject is empty
			if (subject === '') {
				alert('Please enter a mail subject.');
				return; // Stop further execution
			}
		
			// Check if greeting is empty
			if (greeting === '') {
				alert('Please enter a mail greeting.');
				return; // Stop further execution
			}
		
			// Check if description is empty
			if (description === '') {
				alert('Please enter a description.');
				return; // Stop further execution
			}
			if (expiry !== "" && (!/^\d+$/.test(expiry) || parseInt(expiry) < 0)) {
				alert("Please enter a valid Expiry Time (a non-negative integer).");
				$('#expiry').focus();
				return false;
			}
			var fields = [
				{ id: "#template_name", name: "TemplateName" },
				{ id: "#type", name: "Type" },
				{ id: "#subject", name: "Subject" },
				{ id: "#greeting", name: "Greeting" },
				
				{ id: "#description", name: "Description" }
			];
		
			for (var i = 0; i < fields.length; i++) {
				var field = fields[i];
				var value = jQuery(field.id).val();
			  
				if (!value) {
				  jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Please Enter ' + field.name + '</div>');
				  return;
				}
			  }
			SecureAjax.post('admin/web_admin_function.php',
				{
					'id': jQuery("#id").val(),
					'template_name': jQuery("#template_name").val(),
					'type': jQuery("#type").val(),
					'subject': jQuery("#subject").val(),
					'greeting': jQuery("#greeting").val(),
					'body': jQuery("#body").val(),
					'description': jQuery("#description").val(),
					'signature': jQuery("#signature").val(),
					'expiry': jQuery("#expiry").val(),
					'action': (jQuery("#id").val() !== '') ? 'update_mail' : 'submit_mail'
				},
				function (response) {
					console.log(response);
					if (response.trim().toLowerCase() === 'success') {
						jQuery('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Mail ' + (jQuery("#id").val() !== '' ? 'updated' : 'inserted') + ' successfully</div>');
						setTimeout(function () {
							var encodedToken = btoa('view_mail');
							window.location.href = "admin_index.php?action=view_mail&token=" + encodeURIComponent(encodedToken);
						}, 2000);
					} else {
						jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + response + '</div>');
					}
				},
				function(error) {
					console.error('Error submitting mail:', error);
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + error + '</div>');
				}
			);
		},
		HandleSmsSubmit: function (e) {
			e.preventDefault();
			console.log('HandleSmsSubmit function triggered');
			var expiry = $("#expiry").val();
			var description = $("#description").val();
			var footer = $("#footer").val();
			var body = $("#body").val();
			var header = $("#header").val();
			var type = $("#type").val();
			var template_name = $("#template_name").val();
			if (template_name === '') {
				alert('Please enter a template name.');
				return; // Stop further execution
			}
			if (type === '') {
				alert('Please enter type.');
				return; // Stop further execution
			}

			if (header === '') {
				alert('Please enter Header.');
				return; // Stop further execution
			}
			if (body === '') {
				alert('Please enter Body.');
				return; // Stop further execution
			}
			if (description === '') {
				alert('Please enter Description.');
				return; // Stop further execution
			}

			if (expiry !== "" && (isNaN(expiry) || parseInt(expiry) <= 0)) {
				alert("Please enter a valid Expiry Time");
				$('#expiry').focus();
				return false;
			}
			var fields = [
				{ id: "#template_name", name: "Template Name" },
				{ id: "#type", name: "Type" },
				{ id: "#header", name: "Header" },
				{ id: "#body", name: "Body" },
				{ id: "#description", name: "Description" }
			];
		
			for (var i = 0; i < fields.length; i++) {
				var field = fields[i];
				var value = jQuery(field.id).val();
		
				if (!value) {
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Please Enter ' + field.name + '</div>');
					return;
				}
			}
			SecureAjax.post('admin/web_admin_function.php',
				{
					'id': jQuery("#id").val(),
					'template_name': jQuery("#template_name").val(),
					'type': jQuery("#type").val(),
					'header': jQuery("#header").val(),
					'body': jQuery("#body").val(),
					'footer': jQuery("#footer").val(),
					'description': jQuery("#description").val(),
					'expiry': jQuery("#expiry").val(),
					'action': (jQuery("#id").val() !== '') ? 'update_sms' : 'submit_sms'
				},
				function (response) {
					console.log(response);
					if (response.trim().toLowerCase() === 'success') {
						jQuery('#success').html('<div class="alert alert-success alert-dismissible" role="alert">SMS Format ' + (jQuery("#id").val() !== '' ? 'updated' : 'inserted') + ' successfully</div>');
						setTimeout(function () {
							var encodedToken = btoa('view_sms');
							window.location.href = "admin_index.php?action=view_sms&token=" + encodeURIComponent(encodedToken);
						}, 2000);
					} else {
						jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + response + '</div>');
					}
				},
				function(error) {
					console.error('Error submitting SMS:', error);
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + error + '</div>');
				}
			);
		},HandlewebchatSubmit: function (e) {
			e.preventDefault();
			if (template_name === '') {
				alert('Please enter a template name.');
				return; // Stop further execution
			}
			if (type === '') {
				alert('Please enter type.');
				return; // Stop further execution
			}

			
			if (body === '') {
				alert('Please enter Content.');
				return; // Stop further execution
			}
			
			var fields = [
				{ id: "#template_name", name: "Template Name" },
				{ id: "#type", name: "Type" },
				{ id: "#body", name: "Body" }
			];
		
			for (var i = 0; i < fields.length; i++) {
				var field = fields[i];
				var value = jQuery(field.id).val();
		
				if (!value) {
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Please Enter ' + field.name + '</div>');
					return;
				}
			}
			SecureAjax.post('admin/web_admin_function.php',
				{
					'id': jQuery("#id").val(),
					'template_name': jQuery("#template_name").val(),
					'type': jQuery("#type").val(),
					'body': jQuery("#body").val(),
					'action': (jQuery("#id").val() !== '') ? 'update_webchat' : 'submit_webchat'
				},
				function (response) {
					console.log(response);
					if (response.trim().toLowerCase() === 'success') {
						jQuery('#success').html('<div class="alert alert-success alert-dismissible" role="alert">WebChat Template ' + (jQuery("#id").val() !== '' ? 'updated' : 'inserted') + ' successfully</div>');
						setTimeout(function () {
							var encodedToken = btoa('webchat_template');
							window.location.href = "admin_index.php?action=view_sms&token=" + encodeURIComponent(encodedToken);
						}, 2000);
					} else {
						jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + response + '</div>');
					}
				},
				function(error) {
					console.error('Error submitting webchat:', error);
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + error + '</div>');
				}
			);
		},HandlewhatsappSubmit: function (e) {
			console.log("whatsapp submit");
			e.preventDefault();
			if (template_name === '') {
				alert('Please enter a template name.');
				return; // Stop further execution
			}
			if (type === '') {
				alert('Please enter type.');
				return; // Stop further execution
			}

			
			if (body === '') {
				alert('Please enter Content.');
				return; // Stop further execution
			}
			
			var fields = [
				{ id: "#template_name", name: "Template Name" },
				{ id: "#type", name: "Type" },
				{ id: "#body", name: "Body" }
			];
		
			for (var i = 0; i < fields.length; i++) {
				var field = fields[i];
				var value = jQuery(field.id).val();
		
				if (!value) {
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Please Enter ' + field.name + '</div>');
					return;
				}
			}
			SecureAjax.post('admin/web_admin_function.php',
				{
					'id': jQuery("#id").val(),
					'template_name': jQuery("#template_name").val(),
					'type': jQuery("#type").val(),
					'body': jQuery("#body").val(),
					'action': (jQuery("#id").val() !== '') ? 'update_whatsapp' : 'submit_whatsapp'
				},
				function (response) {
					console.log(response);
					if (response.trim().toLowerCase() === 'success') {
						jQuery('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Whatsapp Template ' + (jQuery("#id").val() !== '' ? 'updated' : 'inserted') + ' successfully</div>');
						setTimeout(function () {
							var encodedToken = btoa('whatsapp_template');
							window.location.href = "admin_index.php?action=view_sms&token=" + encodeURIComponent(encodedToken);
						}, 2000);
					} else {
						jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + response + '</div>');
					}
				},
				function(error) {
					console.error('Error submitting whatsapp:', error);
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + error + '</div>');
				}
			);
		},
		HandleProjectSubmit: function(e) {
			e.preventDefault();
		
			var category = $('#category').val();
			if (!category) {
				alert("Please choose a Category!");
				$('#category').focus();
				return false;
			}
		
			var projectname = $('#vProjectName').val();
			var ProjectRegex = /^[a-zA-Z\s]+$/;
			if (!projectname) {
				alert("Please enter Project Name!");
				$('#vProjectName').focus();
				return;
			}
		
			if (!ProjectRegex.test(projectname)) {
				alert("Please enter a valid Project Name!");
				$('#vProjectName').focus();
				return;
			}
		
			SecureAjax.post('admin/web_admin_function.php',
				{
					'category': category,
					'vProjectName': projectname,
					'id': jQuery("#id").val(),
					'action': (jQuery("#id").val() !== '') ? 'update_project' : 'submit_project'
				},
				function(response) {
					console.log(response);
					if (response.trim().toLowerCase() === 'success') {
						jQuery('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Project ' + (jQuery("#id").val() !== '' ? 'updated' : 'inserted') + ' successfully</div>');
						setTimeout(function() {
							var encodedToken = btoa('view_project');
							window.location.href = "admin_index.php?action=view_project&token=" + encodeURIComponent(encodedToken);
						}, 2000);
					} else {
						jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + response + '</div>');
					}
				},
				function(error) {
					console.error('Error submitting project:', error);
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + error + '</div>');
				}
			);
		},
		
		HandleStatusSubmit : function(e){
			e.preventDefault();
			var id = jQuery("#id").val();
			var status = jQuery("#status").val();
			var action = (id !== '') ? 'update_status' : 'submit_status';
		
			if(status.trim() === ''){
				alert('Please enter Status Name!');
				return;
			}
			var statusRegex = /^[a-zA-Z\s]+$/;

			if (!statusRegex.test(status)) {
				alert('Please enter valid Status Name!');
				return;
			}

			SecureAjax.post('admin/web_admin_function.php',
				{ 'id': id, 'status': status, 'action': action },
				function (response) {
					if (response.trim().toLowerCase() === 'success') {
						jQuery('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Province ' + (id !== '' ? 'updated' : 'inserted') + ' successfully</div>');
						setTimeout(function () {
							var encodedToken = btoa('view_status');
							window.location.href = "admin_index.php?action=view_province&token=" + encodeURIComponent(encodedToken);
						}, 2000);
					} else {
						jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + response + '</div>');
					}
					
				},
				function(error) {
					console.error('Error submitting status:', error);
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + error + '</div>');
				}
			);
			
		},
		HandleSpamSubmit :function(e){
			e.preventDefault();

			var mail = jQuery('#mail').val();
			var action = 'spam_mail';
			SecureAjax.post('admin/web_admin_function.php',
				{'mail':mail ,'action':action},
				function(response){
					if(response.trim().toLowerCase() === 'success'){
						jQuery('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Mail Id added to spam successfully</div>');
						setTimeout(function () {
							var encodedToken = btoa('spam_mail');
							window.location.href = "admin_index.php?action=view_province&token=" + encodeURIComponent(encodedToken);
						}, 2000);
					}
				},
				function(error) {
					console.error('Error submitting spam:', error);
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + error + '</div>');
				}
			);
		},
		HandleBaseSubmit(e) {
			e.preventDefault();
			
			var id = jQuery("#id").val();
			var v_qus = jQuery("#qus").val();
			var v_ans = jQuery("#ans").val();
		
			if (!v_qus.trim() || !v_ans.trim()) {
				console.log("Showing alert: Please enter both Question and Answer.");
				alert('Please enter both Question and Answer.');
				return;
			}
		
			var action = (id !== '') ? 'update_base' : 'submit_base';
		
			SecureAjax.post('admin/web_admin_function.php',
				{ 'i_id': id, 'v_qus': v_qus, 'v_ans': v_ans, 'action': action },
				function (response) {
					console.log('AJAX Response:', response);
					var jsonResponse;
					try {
						jsonResponse = JSON.parse(response);
					} catch (e) {
						console.log('Invalid JSON response:', response);
						jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Invalid server response</div>');
						return;
					}
		
					if (jsonResponse.status === 'success') {
						jQuery('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Knowledge Base ' + (id !== '' ? 'updated' : 'inserted') + ' successfully</div>');
						setTimeout(function () {
							var encodedToken = btoa('view_base');
							window.location.href = "admin_index.php?action=view_base&token=" + encodeURIComponent(encodedToken);
						}, 2000);
					} else {
						jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + jsonResponse.message + '</div>');
					}
				},
				function(error) {
					console.error('Error submitting base:', error);
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + error + '</div>');
				}
			);
		},
		HandleBulkSubmit(e) {
			e.preventDefault();
		
			var id = jQuery("#id").val();  // Make sure the correct value is retrieved
			var type = jQuery("#type").val();
			var slug = jQuery("#slug").val();
			var template_name = jQuery("#template_name").val();
			var template_content = jQuery("#template_content").val();
		
			// Check for empty fields and alert the user
			if (!type) {
				alert("Type cannot be empty!");
				return;
			}
			if (!template_name) {
				alert("Template name cannot be empty!");
				return;
			}
			if (!template_content) {
				alert("Template content cannot be empty!");
				return;
			}
			console.log("update bulk clicked");
		
			var action = "update_bulk_data"; // Action to update the template
		
			
		
			SecureAjax.post('admin/web_admin_function.php',
				{
					'id': id,  // Ensure 'id' is correctly passed
					'template_name': template_name,
					'template_content': template_content,
					'action': action,
					'slug':slug,
					'type': type
				},
				function (response) {
					console.log('AJAX Response:', response);
					var jsonResponse;
					try {
						jsonResponse = JSON.parse(response);  // Parse the JSON response
					} catch (e) {
						console.log('Invalid JSON response:', response); // Handle invalid JSON
						jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: Invalid server response</div>');
						return;
					}
		
					if (jsonResponse.status === 'success') {
						jQuery('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Template ' + (id !== '' ? 'updated' : 'inserted') + ' successfully</div>');
						setTimeout(function () {
							var encodedToken = btoa('email_sms_template');
							window.location.href = "admin_index.php?action=view_base&token=" + encodeURIComponent(encodedToken);
						}, 2000);
					} else {
						jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + jsonResponse.message + '</div>');
					}
				},
				function(error) {
					console.error('Error submitting bulk:', error);
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + error + '</div>');
				}
			);
		},
		
		HandleEscalationSubmit: function(e) {
			e.preventDefault();
		
			var id = jQuery("#id").val();
			var escalation_to = jQuery("input[name='escalation_to']:checked").val();
			var escalation_media = jQuery("input[name='escalation_media']:checked").val();
			var escalation_list = jQuery("#escalation_list option:selected").map(function() {
				return this.value;
			}).get();
		
			SecureAjax.post('admin/web_admin_function.php',
				{
					'id': id,
					'escalation_to': escalation_to,
					'escalation_media': escalation_media,
					'escalation_list': escalation_list,
					'action': 'Submit_escalation'
				},
				function(response) {
					console.log('Response from server:', response);
					if (response.trim().toLowerCase() === 'success') {
						console.log('Status Updated Successfully');
						jQuery('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Status Updated Successfully</div>');
						setTimeout(function() {
							var encodedToken = btoa('view_escalation');
							window.location.href = "admin_index.php?action=view_escalation&token=" + encodeURIComponent(encodedToken);
						}, 2000);
					} else {
						console.log('Error updating status:', response);
						jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + response + '</div>');
					}
				},
				function(error) {
					console.error('Error submitting escalation:', error);
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + error + '</div>');
				}
			);
		},
		
		HandleSMTPSubmit: function (e) {
			e.preventDefault();
		
			var id = jQuery("#id").val();
			var i_port = jQuery("#i_port").val();
			var v_username = jQuery("#v_username").val();
			var v_password = jQuery("#v_password").val();
			var v_server = jQuery("#v_server").val();
			var i_tls = jQuery("#i_tls").val();
			var i_debug = jQuery("#i_debug").val();
		
			if (!i_port || !v_username || !v_password || !v_server || !i_tls || !i_debug) {
				alert("Please fill in all required fields!");
				return false;
			}
		
			if (!/^\d+$/.test(i_port)) {
				alert("Please enter a valid port with only numbers!");
				jQuery("#i_port").focus();
				return false;
			}
			if (!/^\d{1,3}$/.test(i_tls)) {
				alert("Please enter a valid TLS!");
				jQuery("#i_tls").focus();
				return false;
			}
		
			if (!/^\d{1,3}$/.test(i_debug)) {
				alert("Please enter a valid DEBUG value!");
				jQuery("#i_debug").focus();
				return false;
			}
		
			SecureAjax.post('admin/web_admin_function.php',
				{
					'id': id,
					'i_port': i_port,
					'v_username': v_username,
					'v_password': v_password,
					'v_server': v_server,
					'i_tls': i_tls,
					'i_debug': i_debug,
					'action': 'update_smtp'
				},
				function (response) {
					if (typeof response !== "object") {
						try {
							response = JSON.parse(response);
						} catch (e) {
							console.error('Error parsing JSON response:', e);
							jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error parsing JSON response</div>');
							return;
						}
					}
		
					if (response.status === 'success') {
						jQuery('#success').html('<div class="alert alert-success alert-dismissible" role="alert">SMTP Settings Updated Successfully</div>');
						setTimeout(function () {
							var encodedToken = btoa('view_imap_smtp');
							window.location.href = "admin_index.php?action=view_imap_smtp&token=" + encodeURIComponent(encodedToken);
						}, 2000);
					} else {
						jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + response.message + '</div>');
					}
				},
				function(error) {
					console.error('Error submitting SMTP:', error);
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + error + '</div>');
				}
			);
		},
		
		HandleIMAPSubmit(e) {
			e.preventDefault();
		
			var id = jQuery("#id").val();
			var v_connectionname = jQuery("#v_connectionname").val();
			var v_ipaddress = jQuery("#v_ipaddress").val();
			var v_username = jQuery("#v_username").val();
			var v_password = jQuery("#v_pasowrd").val(); // Ensure this matches the PHP variable name
			var v_type = jQuery("#v_type").val();
			var v_client_id = jQuery("#v_client_id").val();
			var v_client_secret = jQuery("#v_client_secret").val();
			var v_tenant = jQuery("#v_tenant").val();
		
			if (v_connectionname === '') {
				alert("Please enter Connection Name");
				return false;
			}
		
			SecureAjax.post('admin/web_admin_function.php',
				{
					'id': id,
					'v_connectionname': v_connectionname,
					'v_ipaddress': v_ipaddress,
					'v_username': v_username,
					'v_pasowrd': v_password, // Ensure this matches the PHP variable name
					'v_type': v_type,
					'v_client_id': v_client_id,
					'v_client_secret': v_client_secret,
					'v_tenant': v_tenant,
					'action': 'update_imap'
				},
				function(response) {
					if (typeof response !== "object") {
						try {
							response = JSON.parse(response);
						} catch (e) {
							console.error('Error parsing JSON response:', e);
							jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error parsing JSON response</div>');
							return;
						}
					}
		
					if (response.status === 'success') {
						console.log('Update successful');
						jQuery('#success').html('<div class="alert alert-success alert-dismissible" role="alert">IMAP Settings Update successful</div>');
						setTimeout(function() {
							var encodedToken = btoa('view_imap_smtp');
							window.location.href = "admin_index.php?action=view_village&token=" + encodeURIComponent(encodedToken);
						}, 2000);
					} else {
						console.error('Update failed. Server returned:', response.message);
						jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Update failed: ' + response.message + '</div>');
					}
				},
				function(error) {
					console.error('Error submitting IMAP:', error);
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + error + '</div>');
				}
			);
		},
		
		HandleDispositionSubmit: function(e){
			e.preventDefault();
		
			var id = $("#id").val();
			var disposition = $("#disposition").val();
			if (disposition  === '') {
				alert('Please enter Disposition.');
				return; // Stop further execution
			}
			var DispoRegex = /^[a-zA-Z\s]+$/;

			if (!DispoRegex.test(disposition)) {
				alert('Please enter valid Disposition');
				return;
			}

			SecureAjax.post('admin/web_admin_function.php',
				{
					'id': id,
					'disposition': disposition,
					'action': (id !== '') ? 'update_disposition' : 'submit_disposition'
				},
				function(response) {
					if (response.trim().toLowerCase() === 'success') {
						console.log('Update successful');
						jQuery('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Updated successful</div>');
						setTimeout(function() {
							var encodedToken = btoa('view_disposition');
							window.location.href = "admin_index.php?action=view_disposition&token=" + encodeURIComponent(encodedToken);
						}, 2000);
					} else {
						console.error('Update failed. Unexpected response:', response);
						jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Unexpected response from the server</div>');
					}
				},
				function(error) {
					console.error('Error submitting disposition:', error);
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + error + '</div>');
				}
			);
		
			return false; 
		},
		HandleCallbacksSubmit: function (e) {
			e.preventDefault();
		
			const form = $('#updateCallbackForm');
			const responseMessage = $('#response-message');
		
			const id = form.find('input[name="id"]').val();
			const callback_time = form.find('input[name="callback_time"]').val();
			const callback_alert_hour = form.find('select[name="callback_alert_hour"]').val();
			const callback_alert_minute = form.find('select[name="callback_alert_minute"]').val();
			const callback_remark = form.find('textarea[name="callback_remark"]').val();
			const csrf_token = form.find('input[name="csrf_token"]').val();
			
			// Basic validation
			if (!callback_time) {
				responseMessage.removeClass('alert-success').addClass('alert-danger').text('Callback date and time are required.').show();
				return;
			}
		
			SecureAjax.post('admin/web_admin_function.php', {
				id: id,
				callback_time: callback_time,
				callback_alert_hour: callback_alert_hour,
				callback_alert_minute: callback_alert_minute,
				callback_remark: callback_remark,
				csrf_token: csrf_token,
				action: 'submit_callbacks'
			}, function (response) {
				responseMessage.removeClass('alert-danger alert-success');
		
				if (response.success) {
					responseMessage.addClass('alert-success').text(response.message || 'Callback updated successfully!').show();
					setTimeout(function () {
						var encodedToken = btoa('view_callbacks');
						window.location.href = "admin_index.php?action=view_callbacks&token=" + encodeURIComponent(encodedToken);
					}, 2000);
				} else {
					responseMessage.addClass('alert-danger').text(response.message || 'An unknown error occurred.').show();
				}
			}, function (error) {
				console.error('Error submitting callback:', error);
				responseMessage.removeClass('alert-success').addClass('alert-danger').text('An AJAX error occurred. Please check the console.').show();
			});
		},
		validateForm: function (e) {
			var user = $('#user').val();
			var assignto = $('input[name="assignto[]"]:checked').length;
			
			
			if (user === '') {
				alert('Please select a user.');
				e.preventDefault(); 
				return;
			}
		
			if (assignto === 0) {
				alert('Please select Township');
				e.preventDefault(); 
				return;
			}
			SecureAjax.post('admin/web_admin_function.php',
				{
					'user': user,
					'assignto': $('input[name="assignto[]"]:checked').map(function () {
						return this.value;
					}).get(),
					'action': 'assignProjectsToUser'
				},
				function (response) {
					location.reload(); 
				},
				function(error) {
					alert('An error occurred during the request.');
				}
			);
		},
		HandleAdhocMail(e){
			e.preventDefault();
			let emailadhoc = jQuery("input[name='emailadhoc']").val();
			SecureAjax.post('admin/web_admin_function.php',
				{
					'emailadhoc': emailadhoc,
					'action': 'Submit_emailadhoc'
				},
				function(response) {
					console.log('Response from server:', response);
					if (response.trim().toLowerCase() === 'success') {
						console.log('Adhoc Email Updated Successfully');
						jQuery('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Adhoc Email Updated Successfully</div>');
						setTimeout(function() {
							var encodedToken = btoa('view_adhoc');
							window.location.href = "admin_index.php?action=view_adhoc&token=" + encodeURIComponent(encodedToken);
						}, 2000);
					} else {
						console.log('Error updating status:', response);
						jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + response + '</div>');
					}
				},
				function(error) {
					console.error('Error submitting adhoc:', error);
					jQuery('#success').html('<div class="alert alert-danger alert-dismissible" role="alert">Error: ' + error + '</div>');
				}
			);
			
		}
	}
	$(document).ready(function () {
		$('#showButton').click(function () {
		   var user = $('#user').val();
	 
		   if (user === '') {
			  alert('Please select a user');
		   }
		});
		
		$("#emailadhoc").prop('disabled', true);
		$("#submitadhoc").prop('disabled', true);
	
		$("#editadhoc").on("click",function(){
			$("#emailadhoc").prop('disabled', false);
			$("#submitadhoc").prop('disabled', false);
	
		});

	 });
	 
	 Admin.init();
});