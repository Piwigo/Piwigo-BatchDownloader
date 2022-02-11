
<form action="{$F_ACTION}" method="post" class="batch_dwn_form" id="request_form">
  <div class="batch_dwn_input_group">
    <label for="firstname">{'First name'|translate}<span>*</span></label>
    <input type="text" name="firstname" id="firstname" size="60">
  </div>
  <div class="batch_dwn_input_group">
    <label for="lastname">{'Last name'|translate}<span>*</span></label>
    <input type="text" name="lastname" id="lastname" size="60">
  </div>
  <div class="batch_dwn_input_group">
    <label for="email">{'Email'|translate}<span>*</span></label>
    <input type="email" name="email" id="email" size="60">
  </div>
  <div class="batch_dwn_input_group">
    <label for="telephone">{'Telephone number'|translate}</label>
    <input type="tel" pattern="[0-9]{10}" name="telephone" id="telephone" size="60">
  </div>
  <div class="batch_dwn_input_group">
    <label for="organisation">{'Organisation'|translate}</label>
    <input type="text" name="organisation" id="organisation" size="60">
  </div>
  <div class="batch_dwn_input_group">
    <label for="profession">{'Profession'|translate}</label>
    <input type="text" name="profession" id="profession" size="60">
  </div>
  <div class="batch_dwn_input_group">
    <label for="reason">{'What are you going to use these photos for?'|translate}<span>*</span></label>
    <input type="text" name="reason" id="reason" size="200">
  </div>
  <div class="batch_dwn_input_group" id="batch_dwn_select_size">
     <label for="batch_dwn_request_size">{'Please choose a photo size for this set'|translate}<span>*</span></label>
{html_options name=batch_dwn_request_size options=$batch_dwn_request_size selected="original" id="batch_dwn_request_size"}
  </div>
  <input type="hidden" name="submit_request">
</form>

