
function Edit_Click(ICategoryID)
{	//alert(ICategoryID);
	with(document.frmService)
	{
		I_ServiceID.value	= ICategoryID;
		Action.value		= 'Edit';
		submit();
	}
}

//====================================================================================================

//	Function Name	:	Delete_Click()

//----------------------------------------------------------------------------------------------------

function Delete_Click(ICategoryID)
{

	with(document.frmService)
	{

		if(confirm('Are you sure you want to delete Services?'))
		{
			I_ServiceID.value	= ICategoryID;
			Action.value		= 'Delete';
			submit();
		}
	}
}



//====================================================================================================

//	Function Name	:	DeleteChecked_Click()

//----------------------------------------------------------------------------------------------------

function DeleteChecked_Click()

{

	with(document.frmService)

	{

		var flg=false;



		if(document.all['srcat_prod[]'].length)

		{

			for(i=0; i < document.all['srcat_prod[]'].length; i++)

			{

				if(document.all['srcat_prod[]'][i].checked)

					flg = true;

			}

		}

		else

		{

			if(document.all['srcat_prod[]'].checked)

				flg = true;

		}



		if(!flg)

		{

			alert('Please select the record you want to delete.');

			return false;

		}

			

		if(confirm('Are you sure you want to delete selected Services ?'))

		{

			Action.value 	= 'DeleteSelected';

			submit();

		}

	}

}