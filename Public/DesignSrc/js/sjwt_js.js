/**
 * Created by gaojie on 2015/8/11.
 */

function ShowMonth(yr){

    HideBox(yr);
    var elementid="#"+yr+"_month";
    $(elementid).slideToggle("fast");

}

function ShowBox(yr,mon)
{
    var allbox = document.getElementsByClassName("eventbox");
    //alert(allbox.length);
    for(var j=0;j<allbox.length;j=j+1){
       allbox[j].style.display='none';
        //allbox[j].style.backgroundColor='#a1c822';
    }
    document.getElementById('NoRecord').style.display = 'block';

    var box = document.getElementsByClassName("eventbox "+ yr+" "+mon);
    for(var i=0;i<box.length;i=i+1){
        box[i].style.display='inline-table';
    }
    if(box.length>0)
    {
        document.getElementById('NoRecord').style.display = 'none';
    }
    var allindicator=document.getElementsByClassName("li_list_indicator");

    for(var i=0;i<allindicator.length;i=i+1)
    {
        //alert("indicator i: "+i)
        allindicator[i].style.backgroundColor="transparent";
    }

    var liindicatorid="li_list_indicator_id_" +yr + "_" + mon;
    //alert(liindicatorid);
    document.getElementById(liindicatorid).style.backgroundColor="#59a642";
    var str=yr+'.'+mon;
    document.getElementById("SelTime").innerText=str;
}

function HideBox(avoidyr)
{

    var mydate=new Date();
    var thisyear=mydate.getFullYear();

    for(var i=2008;i<=thisyear;i++)
    {
        if(i!=avoidyr){
            var element="#"+i+"_month";
            $(element).slideUp("fast");
        }

    }

}