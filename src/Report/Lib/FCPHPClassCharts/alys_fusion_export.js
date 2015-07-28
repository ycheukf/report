if(!alys_re_export)
	var alys_re_export = {chartObjects:[]};

function fusionChartLoading(DomID) 
{					
	alys_re_export.chartObjects[DomID] = getChartFromId(DomID);
	if(alys_re_export.chartObjects[DomID].hasRendered){  				
		alys_re_export.chartObjects[DomID].exportChart();//{exportFormat: 'png' }				
	}
};
function myexportCallBack(exporStatus)
{				
	var returnValue = exporStatus.fileName;
	if(exporStatus.statusCode == '1'){
		var saveFilenameObj=returnValue.match(/[^/]+$/);
		if(alys_re_export.pdf_export_location.indexOf("?")>0){
			alys_re_export.pdf_export_location +='&alys='+saveFilenameObj[0];	
		}else{
			alys_re_export.pdf_export_location +='?alys='+saveFilenameObj[0];
		}
		window.location.href= alys_re_export.pdf_export_location;  
	}
	else alert("All Charts Are Not Exported Successfully.");
};