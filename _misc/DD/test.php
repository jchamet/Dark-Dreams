<input type="radio" id="work_abroad_y" name="work_abroad" value="y" /><label for="work_abroad_y">Yes</label>
<input type="radio" id="work_abroad_n" name="work_abroad" value="n" /><label for="work_abroad_n">No</label>
<input type=

<script>
function getRadioCheckedValue(radio_name)
{
   var oRadio = document.forms[0].elements[radio_name];
   for(var i = 0; i < oRadio.length; i++)
   {
      if(oRadio[i].checked)
      {
         return oRadio[i].value;
      }
   }
   return '';
}
</script>